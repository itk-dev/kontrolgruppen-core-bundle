<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\KL;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Exception;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Traversable;

/**
 * Class Export.
 */
class Export extends AbstractExport
{
    protected $title = 'KL';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var EconomyService */
    private $economyService;

    /**
     * Export constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EconomyService         $economyService
     * @param CacheItemPoolInterface $cachePhpspreadsheet
     *
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, EconomyService $economyService, CacheItemPoolInterface $cachePhpspreadsheet)
    {
        parent::__construct($cachePhpspreadsheet);
        $this->entityManager = $entityManager;
        $this->economyService = $economyService;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return parent::getParameters() + [
                'processtatus' => [
                    'type' => EntityType::class,
                    'type_options' => [
                        'label' => 'process.table.process_status',
                        'class' => ProcessStatus::class,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s')
                                ->orderBy('s.name', 'ASC');
                        },
                        'required' => false,
                        'empty_data' => null,
                    ],
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->writeRow([
            'Overføres ikke til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Kun afsluttede sager overføres til KL',
            'Overføres ikke til KL',
        ]);

        $this->writeHeader([
            'Sag',
            'Kanal',
            'Sagstype',
            'Ydelsestype',
            'Er borgeren selvstændig erhvervsdrivende?',
            'Har der været udført virksomhedskontrol?',
            'Samlet tilbagebetalingskrav i kr.',
            'Samlet fremadrettet besparelse ved ydelsesstop i kr.',
            'Videresendes til anden myndighed',
            'Er sagen blevet politianmeldt?',
            'Status',
            'Diverse',
        ]);

        foreach ($processes as $process) {
            $rows = $this->createRowsArray($process);

            foreach ($rows as $row) {
                $row['futureSavingsSum'] = $this->formatNumber($row['futureSavingsSum'], 2);
                $row['repaymentSum'] = $this->formatNumber($row['repaymentSum'], 2);

                $this->writeRow(array_values($row));
            }
        }
    }

    /**
     * Create rows for a process.
     *
     * @param Process $process
     *
     * @return array
     */
    private function createRowsArray(Process $process)
    {
        $processRevenue = $this->economyService->calculateRevenue($process);

        $revenue = [];

        foreach ($processRevenue['repaymentSums'] as $serviceName => $repaymentSum) {
            if (!isset($revenue[$serviceName])) {
                $revenue[$serviceName] = $this->getNewRow($process, $serviceName);
            }

            $revenue[$serviceName]['repaymentSum'] += $repaymentSum['sum'];
        }

        foreach ($processRevenue['futureSavingsSums'] as $serviceName => $futureSavingsSum) {
            if (!isset($revenue[$serviceName])) {
                $revenue[$serviceName] = $this->getNewRow($process, $serviceName);
            }

            $revenue[$serviceName]['futureSavingsSum'] += $futureSavingsSum['sum'];
        }

        // Include the process, even if it has no revenue set,
        // with the name of the service set for the process.
        if (empty($processRevenue['repaymentSums']) && empty($processRevenue['futureSavingsSums'])) {
            $serviceName = $process->getService() ? $process->getService()->getName() : '-- Ikke sat --';
            $revenue[$serviceName] = $this->getNewRow($process, $serviceName);
        }

        return $revenue;
    }

    /**
     * Create a new row.
     *
     * @param Process $process
     * @param         $serviceName
     *
     * @return array
     */
    private function getNewRow(Process $process, $serviceName)
    {
        $forwardedTo = $process->getForwardedToAuthorities();
        $forwardedTo = \count($forwardedTo) > 0 ? $forwardedTo[0] : null;

        return [
            'caseNumber' => $process->getCaseNumber(),
            'channel' => $process->getChannel() ? $process->getChannel()->getName() : null,
            'processType' => $process->getProcessType() ? $process->getProcessType()->getName() : null,
            'service' => $serviceName,
            'clientHasOwnCompany' => $this->formatBooleanYesNoNull($process->getClient()->getHasOwnCompany()),
            'performedCompanyCheck' => $this->formatBooleanYesNoNull($process->getPerformedCompanyCheck()),
            'repaymentSum' => 0.0,
            'futureSavingsSum' => 0.0,
            'isForwardedToAnotherAuthority' => $forwardedTo,
            'policeReport' => $this->formatBooleanYesNoNull($process->getPoliceReport()),
            'status' => $process->getProcessStatus() ? $process->getProcessStatus()->getName() : null,
            'misc' => null,
        ];
    }

    /**
     * @throws Exception
     *
     * @return Traversable
     */
    private function getProcesses()
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)
            ->createQueryBuilder('p');

        $queryBuilder->andWhere('p.completedAt IS NOT NULL');

        if (!empty($this->parameters['processtatus'])) {
            $queryBuilder
                ->andWhere('p.processStatus = :processtatus')
                ->setParameter('processtatus', $this->parameters['processtatus']);
        }

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        $queryBuilder
            ->andWhere('p.completedAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return SimpleBatchIteratorAggregate::fromQuery(
            $queryBuilder->getQuery(),
            100
        );
    }
}

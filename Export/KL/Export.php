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
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class Export extends AbstractExport
{
    protected $title = 'KL';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, EconomyService $economyService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->economyService = $economyService;
    }

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
            'Kun afsluttede sager overføres til KL',
            'Overføres ikke til KL',
        ]);

        $this->writeHeader([
            'Sag',
            'Kanal',
            'Sagstype',
            'Ydelsestype',
            'Er borgeren selvstændig erhvervsdrivende?',
            'Samlet tilbagebetalingskrav i kr.',
            'Samlet fremadrettet besparelse ved ydelsesstop i kr.',
            'Videresendes til anden myndighed',
            'Er sagen blevet politianmeldt?',
            'Status',
            'Diverse',
        ]);

        foreach ($processes as $process) {
            $revenue = $this->economyService->calculateRevenue($process);

            $this->writeRow([
                $process->getCaseNumber(),
                $process->getChannel() ? $process->getChannel()->getName() : null,
                $process->getProcessType() ? $process->getProcessType()->getName() : null,
                $process->getService() ? $process->getService()->getName() : null,
                $this->formatBoolean($process->getClient() && $process->getClient()->getSelfEmployed()),
                $this->formatAmount($revenue['collectiveNetSum'] ?? 0),
                $this->formatAmount($revenue['futureSavingsSum'] ?? 0),
                $this->formatBoolean($process->getProcessStatus() && $process->getProcessStatus()->getIsForwardToAnotherAuthority()),
                $this->formatBoolean($process->getPoliceReport()),
                $process->getProcessStatus() ? $process->getProcessStatus()->getName() : null,
                null,
            ]);
        }
    }

    /**
     * @return Process[]
     */
    private function getProcesses()
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)->createQueryBuilder('p')
            ->andWhere('p.completedAt IS NOT NULL');

        if (!empty($this->parameters['processtatus'])) {
            $queryBuilder
                ->andWhere('p.processStatus = :processtatus')
                ->setParameter('processtatus', $this->parameters['processtatus']);
        }

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        $queryBuilder
            ->andWhere('p.createdAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return $queryBuilder->getQuery()->execute();
    }
}

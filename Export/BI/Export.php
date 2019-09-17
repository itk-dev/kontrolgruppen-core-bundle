<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\BI;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Repository\ProcessLogEntryRepository;
use Kontrolgruppen\CoreBundle\Service\EconomyService;

class Export extends AbstractExport
{
    protected $title = 'BI';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var EconomyService */
    private $economyService;

    /** @var ProcessLogEntryRepository */
    private $processLogEntryRepository;

    public function __construct(EntityManagerInterface $entityManager, EconomyService $economyService, ProcessLogEntryRepository $processLogEntryRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->economyService = $economyService;
        $this->processLogEntryRepository = $processLogEntryRepository;
    }

    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->writeHeader([
            'Udtræksdato',
            'Sagsnummer',
            'Oprettet dato',
            'Sagsbehandler',
            'CPR-nummer',
            'Antal børn',
            'Antal biler',
            'Oprettet status',
            'Sagstype',
            'Sagsstatus',
            'Dato for sagsstatus',
            'Årsager',
            'Kanaler',
            'Ydelser',
            'Offentlig forsørgelse',
            'Ansat',
            'Er borgeren selvstændig erhvervsdrivende?',
            'Videresendes til anden myndighed',
            'Er sagen politianmeldt?',
            'Rettens afgørelse',
            'Samlet tilbagebetalingskrav i kr.',
            'Netto samlet tilbagebetalingskrav i kr.',
            'Samlet fremadrettet besparelse ved ydelsesstop i kr.',
            'Netto fremadrettet besparelse ved ydelsesstop i kr.',
            'Samlet opgørelse',
            'Samlet nettoopgørelse',
        ]);

        foreach ($processes as $process) {
            $revenue = $this->economyService->calculateRevenue($process);

            // Get first and latest change of processStatus.
            $logEntries = array_filter(
                $this->processLogEntryRepository->getAllLogEntries($process),
                static function (ProcessLogEntry $entry) {
                    return null !== $entry->getLogEntry()
                        && Process::class === $entry->getLogEntry()->getObjectClass()
                        && \array_key_exists('processStatus', $entry->getLogEntry()->getData());
                }
            );
            $latestLogEntry = reset($logEntries);
            $firstLogEntry = end($logEntries);

            $this->writeRow([
                new \DateTime(), // 'Udtræksdato'
                $process->getCaseNumber(), // 'Sagsnummer'
                $process->getCreatedAt(), // 'Oprettet dato'
                // @TODO How do we/they identify a case worker?
                $process->getCaseWorker() ? $process->getCaseWorker()->getUsername() : null, // 'Sagsbehandler'
                $process->getClientCPR(), // 'CPR-nummer'
                $process->getClient() ? $process->getClient()->getNumberOfChildren() : null, // 'Antal børn'
                $process->getClient() ? $process->getClient()->getCars()->count() : null, // 'Antal biler'
                $firstLogEntry ? $firstLogEntry->getLogEntry()->getLoggedAt() : null, // 'Oprettet status'
                $process->getProcessType() ? $process->getProcessType()->getName() : null, // 'Sagstype'
                $process->getProcessStatus() ? $process->getProcessStatus()->getName() : null, // 'Sagsstatus'
                $latestLogEntry ? $latestLogEntry->getLogEntry()->getLoggedAt() : null, // 'Dato for sagsstatus'
                $process->getReason() ? $process->getReason()->getName() : null, // 'Årsager'
                $process->getChannel() ? $process->getChannel()->getName() : null, // 'Kanaler'
                $process->getService() ? $process->getService()->getName() : null, // 'Ydelser'
                $this->formatBoolean($process->getClient() && $process->getClient()->getReceivesPublicAid()), // 'Offentlig forsørgelse'
                $this->formatBoolean($process->getClient() && $process->getClient()->getEmployed()), // 'Ansat'
                $this->formatBoolean($process->getClient() && $process->getClient()->getHasOwnCompany()), // 'Er borgeren selvstændig erhvervsdrivende?'
                $this->formatBoolean($process->getProcessStatus() && $process->getProcessStatus()->getIsForwardToAnotherAuthority()), // 'Videresendes til anden myndighed'
                $this->formatBoolean((bool) $process->getPoliceReport()), // 'Er sagen politianmeldt?'
                $this->formatBoolean((bool) $process->getCourtDecision()), // 'Rettens afgørelse'
                $this->formatAmount($revenue['repaymentSum'] ?? 0), // 'Samlet tilbagebetalingskrav i kr.'
                $this->formatAmount($revenue['netRepaymentSum'] ?? 0), // 'Netto samlet tilbagebetalingskrav i kr.'
                $this->formatAmount($revenue['futureSavingsSum'] ?? 0), // 'Samlet fremadrettet besparelse ved ydelsesstop i kr.'
                $this->formatAmount($revenue['netFutureSavingsSum'] ?? 0), // 'Netto fremadrettet besparelse ved ydelsesstop i kr.'
                $this->formatAmount($revenue['collectiveSum'] ?? 0), // 'Samlet opgørelse'
                $this->formatAmount($revenue['netCollectiveSum'] ?? 0), // 'Samlet nettoopgørelse'
            ]);
        }
    }

    /**
     * @return Process[]
     */
    private function getProcesses()
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)
            ->createQueryBuilder('p');

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        $queryBuilder
            ->andWhere('p.createdAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return $queryBuilder->getQuery()->execute();
    }
}

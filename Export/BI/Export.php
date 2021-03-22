<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\BI;

use Exception;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Repository\ProcessLogEntryRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Export.
 */
class Export extends AbstractExport
{
    protected $title = 'BI';

    /** @var EconomyService */
    private $economyService;

    /** @var ProcessLogEntryRepository */
    private $processLogEntryRepository;

    /** @var ProcessRepository */
    private $processRepository;

    /**
     * Export constructor.
     *
     * @param EconomyService            $economyService
     * @param ProcessLogEntryRepository $processLogEntryRepository
     * @param ProcessRepository         $processRepository
     * @param CacheItemPoolInterface    $cachePhpspreadsheet
     *
     * @throws Exception
     */
    public function __construct(EconomyService $economyService, ProcessLogEntryRepository $processLogEntryRepository, ProcessRepository $processRepository, CacheItemPoolInterface $cachePhpspreadsheet)
    {
        parent::__construct($cachePhpspreadsheet);
        $this->economyService = $economyService;
        $this->processLogEntryRepository = $processLogEntryRepository;
        $this->processRepository = $processRepository;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function writeData()
    {
        $processes = $this->processRepository->findAllBatchProcessed();

        $this->writeHeader([
            'Udtræksdato',
            'Sagsnummer',
            'Oprettet dato',
            'Sagsbehandler',
            'Postnummer',
            'CPR-nummer',
            'Antal børn',
            'Antal biler',
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
            'Oprindeligt afsluttet',
            'Senest afsluttet',
            'Senest genoptaget',
            'Samlet nettoopgørelse difference',
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

            $forwardedTo = $process->getForwardedToAuthorities();
            $forwardedTo = \count($forwardedTo) > 0 ? $forwardedTo[0] : null;

            $this->writeRow([
                $this->formatDate(new \DateTime()), // 'Udtræksdato'
                $process->getCaseNumber(), // 'Sagsnummer'
                $this->formatDate($process->getCreatedAt(), 'long'), // 'Oprettet dato'
                $process->getCaseWorker() ? $process->getCaseWorker()->getUsername() : null, // 'Sagsbehandler'
                $process->getClient() ? $process->getClient()->getPostalCode() : null, // 'Postnummer'
                $process->getClientCPR(), // 'CPR-nummer'
                $process->getClient() ? $process->getClient()->getNumberOfChildren() : null, // 'Antal børn'
                $process->getClient() ? $process->getClient()->getCars()->count() : null, // 'Antal biler'
                $process->getProcessType() ? $process->getProcessType()->getName() : null, // 'Sagstype'
                $process->getProcessStatus() ? $process->getProcessStatus()->getName() : null, // 'Sagsstatus'
                $latestLogEntry ? $this->formatDate($latestLogEntry->getLogEntry()->getLoggedAt(), 'long') : null, // 'Dato for sagsstatus'
                $process->getReason() ? $process->getReason()->getName() : null, // 'Årsager'
                $process->getChannel() ? $process->getChannel()->getName() : null, // 'Kanaler'
                $process->getService() ? $process->getService()->getName() : null, // 'Ydelser'
                $this->formatBooleanYesNoNull($process->getClient() && $process->getClient()->getReceivesPublicAid()), // 'Offentlig forsørgelse'
                $this->formatBooleanYesNoNull($process->getClient() && $process->getClient()->getEmployed()), // 'Ansat'
                $this->formatBooleanYesNoNull($process->getClient() && $process->getClient()->getHasOwnCompany()), // 'Er borgeren selvstændig erhvervsdrivende?'
                $forwardedTo, // 'Videresendes til anden myndighed'
                $this->formatBooleanDecision($process->getPoliceReport()), // 'Er sagen politianmeldt?'
                $this->formatBooleanYesNoNull($process->getCourtDecision()), // 'Rettens afgørelse'
                $this->formatAmount($revenue['repaymentSum'] ?? 0), // 'Samlet tilbagebetalingskrav i kr.'
                $this->formatAmount($revenue['netRepaymentSum'] ?? 0), // 'Netto samlet tilbagebetalingskrav i kr.'
                $this->formatAmount($revenue['futureSavingsSum'] ?? 0), // 'Samlet fremadrettet besparelse ved ydelsesstop i kr.'
                $this->formatAmount($revenue['netFutureSavingsSum'] ?? 0), // 'Netto fremadrettet besparelse ved ydelsesstop i kr.'
                $this->formatAmount($revenue['collectiveSum'] ?? 0), // 'Samlet opgørelse'
                $this->formatAmount($revenue['netCollectiveSum'] ?? 0), // 'Samlet nettoopgørelse'
                $process->getOriginallyCompletedAt() ? $this->formatDate($process->getOriginallyCompletedAt(), 'long') : null,
                $process->getLastCompletedAt() ? $this->formatDate($process->getLastCompletedAt(), 'long') : null,
                $process->getLastReopened() ? $this->formatDate($process->getLastReopened(), 'long') : null,
                $this->formatAmount($process->getNetCollectiveSumDifference() ?? 0),
            ]);
        }
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\Logs;

use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Service\ProcessLogTranslatorService;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ProcessLogExport
 */
class ProcessLogExport extends AbstractExport
{
    protected $title = 'ProcessLog';

    private $entityManager;
    private $processLogTranslatorService;
    private $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ProcessLogTranslatorService $processLogTranslatorService
    )
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->processLogTranslatorService = $processLogTranslatorService;
        $this->translator = $translator;
    }

    public function writeData()
    {
        $this->writeRow([
            $this->translator->trans('process_log.revision.table.date'),
            $this->translator->trans('process_log.revision.table.action'),
            $this->translator->trans('process_log.revision.table.user'),
        ]);

        $process = $this->getParameterValue('process');

        $processLogEntries = $this->entityManager
                        ->getRepository(ProcessLogEntry::class)
                        ->getAllLogEntries($process);

        foreach ($processLogEntries as $processLogEntry) {
            /** @var LogEntry $logEntry */
            $logEntry = $processLogEntry->getLogEntry();

            $action = $this->processLogTranslatorService->translateObjectClass($logEntry->getObjectClass())
                    .' '
                    .$this->processLogTranslatorService->translateAction($logEntry->getAction());

            $this->writeRow([
                $logEntry->getLoggedAt(),
                $action,
                $logEntry->getUsername(),
            ]);

            if (!empty($logEntry->getData())) {
                $logEntryData = $logEntry->getData();

                foreach ($logEntryData as $key => $value) {
                    $value = (\is_array($value)
                                ? $value['name']
                                : $value);

                    $this->writeRow(
                        [
                        $this->processLogTranslatorService->translateDataKey($key, $logEntry->getObjectClass()),
                        $value,
                        ],
                        null,
                        1
                    );
                }
            }
        }
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryEnumType;
use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Kontrolgruppen\CoreBundle\Repository\JournalEntryRepository;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class ReportService.
 */
class ReportService
{
    private $conclusionService;
    private $economyEntryRepository;
    private $journalEntryRepository;
    private $pdf;
    private $translator;
    private $twig;

    public function __construct(
        ConclusionService $conclusionService,
        EconomyEntryRepository $economyEntryRepository,
        Environment $twig,
        JournalEntryRepository $journalEntryRepository,
        TranslatorInterface $translator
    ) {
        $this->conclusionService = $conclusionService;
        $this->economyEntryRepository = $economyEntryRepository;
        $this->journalEntryRepository = $journalEntryRepository;
        $this->pdf = new Mpdf();
        $this->translator = $translator;
        $this->twig = $twig;
    }

    public function generateProcessReport(Process $process, string $choice = 'only_summary'): string
    {
        $viewData = [
            'process' => $process,
            'choice' => $choice,
        ];

        $viewData['economyEntries'] = [
            'service' => $this->economyEntryRepository->findBy([
                'process' => $process,
                'type' => EconomyEntryEnumType::SERVICE,
            ]),
            'account' => $this->economyEntryRepository->findBy([
                'process' => $process,
                'type' => EconomyEntryEnumType::ACCOUNT,
            ]),
            'arrear' => $this->economyEntryRepository->findBy([
                'process' => $process,
                'type' => EconomyEntryEnumType::ARREAR,
            ]),
        ];

        $qb = $this->journalEntryRepository->createQueryBuilder('journalEntry');
        $qb
            ->select('journalEntry')
            ->where('journalEntry.process = :process')
            ->setParameter('process', $process)
            ->andWhere('journalEntry.type = :note')
            ->setParameter('note', JournalEntryEnumType::NOTE);

        if ('internal_notes' === $choice) {
            $qb
                ->orWhere('journalEntry.type = :internal')
                ->setParameter('internal', JournalEntryEnumType::INTERNAL_NOTE);
        }

        $viewData['journalEntries'] = $qb->getQuery()->getArrayResult();

        $viewData['conclusionTemplate'] = $this->conclusionService->getTemplate(
            \get_class($process->getConclusion()),
            '',
            '@KontrolgruppenCore/process_report/'
        );

        $reportHtml = $this->twig->render('@KontrolgruppenCore/process_report/_report.html.twig', $viewData);

        $this->pdf->WriteHtml($reportHtml);

        $filename = sprintf(
            '%s-%s.pdf',
            strtolower($this->translator->trans('process_report.report.title')),
            $process->getCaseNumber()
        );

        $this->pdf->Output($filename, Destination::FILE);

        return $filename;
    }
}

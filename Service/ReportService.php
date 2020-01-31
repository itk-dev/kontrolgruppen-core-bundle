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

    /**
     * ReportService constructor.
     *
     * @param ConclusionService      $conclusionService
     * @param EconomyEntryRepository $economyEntryRepository
     * @param Environment            $twig
     * @param JournalEntryRepository $journalEntryRepository
     * @param TranslatorInterface    $translator
     *
     * @throws \Mpdf\MpdfException
     */
    public function __construct(ConclusionService $conclusionService, EconomyEntryRepository $economyEntryRepository, Environment $twig, JournalEntryRepository $journalEntryRepository, TranslatorInterface $translator)
    {
        $this->conclusionService = $conclusionService;
        $this->economyEntryRepository = $economyEntryRepository;
        $this->journalEntryRepository = $journalEntryRepository;
        $this->pdf = new Mpdf();
        $this->translator = $translator;
        $this->twig = $twig;
    }

    /**
     * @param Process $process
     * @param string  $choice
     *
     * @return string
     *
     * @throws \Mpdf\MpdfException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
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
            'income' => $this->economyEntryRepository->findBy([
                'process' => $process,
                'type' => EconomyEntryEnumType::INCOME,
            ]),
        ];

        $qb = $this->journalEntryRepository->createQueryBuilder('journalEntry');
        $qb
            ->select('journalEntry')
            ->where('journalEntry.process = :process')
            ->setParameter('process', $process);

        $journalTypes = [JournalEntryEnumType::NOTE];

        if ('internal_notes' === $choice) {
            $journalTypes[] = JournalEntryEnumType::INTERNAL_NOTE;
        }

        $qb
            ->andWhere('journalEntry.type IN (:journalTypes)')
            ->setParameter(':journalTypes', $journalTypes);

        if ('only_summary' === $choice) {
            $qb
                ->setMaxResults(5)
                ->orderBy('journalEntry.createdAt', 'desc');
        }

        $viewData['journalEntries'] = $qb->getQuery()->getArrayResult();

        if (!empty($process->getConclusion())) {
            $viewData['conclusionTemplate'] = $this->conclusionService->getTemplate(
                \get_class($process->getConclusion()),
                '',
                '@KontrolgruppenCore/process_report/'
            );
        }

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

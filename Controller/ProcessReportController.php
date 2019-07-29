<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryEnumType;
use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Kontrolgruppen\CoreBundle\Repository\JournalEntryRepository;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Mpdf\Mpdf;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process/{process}/report")
 */
class ProcessReportController extends BaseController
{
    /**
     * @Route("/", name="process_report_index", methods={"GET","POST"})
     */
    public function index(
        Request $request,
        Process $process,
        TranslatorInterface $translator,
        EconomyEntryRepository $economyEntryRepository,
        ConclusionService $conclusionService,
        JournalEntryRepository $journalEntryRepository
    ): Response {
        $form = $this->createFormBuilder()
            ->add('options', ChoiceType::class, [
                'label' => $translator->trans('process_report.form.choices.placeholder'),
                'choices' => [
                    $translator->trans('process_report.form.choices.internal_notes') => 'internal_notes',
                    $translator->trans('process_report.form.choices.only_summary') => 'only_summary',
                ],
            ])
            ->add('generate', SubmitType::class, [
                'label' => $translator->trans('process_report.form.submit'),
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $viewData = [
                'process' => $process,
                'choice' => $formData['options'],
            ];

            $viewData['economyEntries'] = [
                'service' => $economyEntryRepository->findBy([
                    'process' => $process,
                    'type' => EconomyEntryEnumType::SERVICE,
                ]),
                'account' => $economyEntryRepository->findBy([
                   'process' => $process,
                   'type' => EconomyEntryEnumType::ACCOUNT,
                ]),
                'arrear' => $economyEntryRepository->findBy([
                   'process' => $process,
                   'type' => EconomyEntryEnumType::ARREAR,
                ]),
            ];

            $qb = $journalEntryRepository->createQueryBuilder('journalEntry');
            $qb
                ->select('journalEntry')
                ->where('journalEntry.process = :process')
                ->setParameter('process', $process)
                ->andWhere('journalEntry.type = :note')
                ->setParameter('note', JournalEntryEnumType::NOTE);

            if ('internal_notes' === $formData['options']) {
                $qb
                    ->orWhere('journalEntry.type = :internal')
                    ->setParameter('internal', JournalEntryEnumType::INTERNAL_NOTE);
            }

            $viewData['journalEntries'] = $qb->getQuery()->getArrayResult();

            $viewData['conclusionTemplate'] = $conclusionService->getTemplate(
                \get_class($process->getConclusion()),
                '',
                '@KontrolgruppenCore/process_report/'
            );

            $report = $this->renderView('@KontrolgruppenCore/process_report/_report.html.twig', $viewData);

            // @TODO Should probably be in a Service
            $mpdf = new Mpdf();
            $mpdf->WriteHTML($report);

            $filename = sprintf(
                '%s-%s.pdf',
                strtolower($translator->trans('process_report.report.title')),
                $process->getCaseNumber()
            );

            $mpdf->Output($filename, 'D');
        }

        $data = [
            'process' => $process,
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'form' => $form->createView(),
        ];

        return $this->render('@KontrolgruppenCore/process_report/index.html.twig', $data);
    }
}

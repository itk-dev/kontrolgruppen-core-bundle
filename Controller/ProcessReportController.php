<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Service\ReportService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process/{process}/report")
 */
class ProcessReportController extends BaseController
{
    /**
     * @Route("/", name="process_report_index", methods={"GET","POST"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param TranslatorInterface $translator
     * @param ReportService       $reportService
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Mpdf\MpdfException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(Request $request, Process $process, TranslatorInterface $translator, ReportService $reportService): Response
    {
        $form = $this->createFormBuilder()
            ->add('options', ChoiceType::class, [
                'label' => $translator->trans('process_report.form.choices.placeholder'),
                'choices' => [
                    $translator->trans('process_report.form.choices.no_internal_notes') => 'no_internal_notes',
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

            $pathToReportFile = $reportService->generateProcessReport($process, $formData['options']);

            $response = new BinaryFileResponse($pathToReportFile);
            $response->deleteFileAfterSend(true);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response;
        }

        $data = [
            'process' => $process,
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'form' => $form->createView(),
        ];

        return $this->render('@KontrolgruppenCore/process_report/index.html.twig', $data);
    }
}

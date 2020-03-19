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
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Export\Logs\ProcessLogExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/log")
 */
class ProcessLogController extends BaseController
{
    /**
     * @Route("/", name="process_log_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Latest Log entries
        $logEntriesPagination = $this->getDoctrine()->getRepository(
            ProcessLogEntry::class
        )->getLatestEntriesPaginated(
            $process,
            $request->query->get('page', 1),
            20
        );

        return $this->render('@KontrolgruppenCoreBundle/process_log/index.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process' => $process,
            'logEntriesPagination' => $logEntriesPagination,
        ]);
    }

    /**
     * @Route("/export", name="process_log_export", methods={"GET"})
     *
     * @param ProcessLogExport $processLogExport
     * @param Process          $process
     *
     * @return Response
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception*@throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export(ProcessLogExport $processLogExport, Process $process): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $spreadsheet = new Spreadsheet();

        $processLogExport->write(['process' => $process], $spreadsheet);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $filename = 'log.xlsx';

        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}

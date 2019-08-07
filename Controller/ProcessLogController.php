<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Gedmo\Loggable\Entity\LogEntry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Service\ProcessLogTranslatorService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process/{process}/log")
 */
class ProcessLogController extends BaseController
{
    private $latestRow = 0;
    private $sheet;

    /**
     * @Route("/", name="process_log_index", methods={"GET","POST"})
     */
    public function index(Request $request, Process $process): Response
    {
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
     */
    public function export(
        TranslatorInterface $translator,
        ProcessLogTranslatorService $processLogTranslatorService,
        Request $request,
        Process $process
    ): Response {
        $spreadsheet = new Spreadsheet();

        $this->sheet = $spreadsheet->getActiveSheet();

        $this->writeRow([
            $translator->trans('process_log.revision.table.date'),
            $translator->trans('process_log.revision.table.action'),
            $translator->trans('process_log.revision.table.user'),
        ]);

        $processLogEntries = $this->getDoctrine()
                        ->getRepository(ProcessLogEntry::class)
                        ->getAllLogEntries($process);

        foreach ($processLogEntries as $processLogEntry) {
            /** @var LogEntry $logEntry */
            $logEntry = $processLogEntry->getLogEntry();

            $action = $processLogTranslatorService->translateObjectClass($logEntry->getObjectClass())
                    .' '
                    .$processLogTranslatorService->translateAction($logEntry->getAction());

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
                        $processLogTranslatorService->translateDataKey($key, $logEntry->getObjectClass()),
                        $value,
                        ],
                        null,
                        1
                    );
                }
            }
        }

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

    private function writeRow(array $row, array $style = null, int $indent = 0)
    {
        ++$this->latestRow;

        foreach ($row as $key => $cell) {
            $colNum = $key + 1 + $indent;

            $this->sheet->setCellValueByColumnAndRow($colNum, $this->latestRow, $cell);
            $styleCell = $this->sheet->getStyleByColumnAndRow($key + 1, $this->latestRow);
            if (null !== $style) {
                $styleCell->applyFromArray($style);
            }
        }
    }
}

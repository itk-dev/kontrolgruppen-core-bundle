<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class PhpSpreadsheetExportService.
 */
class PhpSpreadsheetExportService
{
    /**
     * Get output from a writer as a string.
     *
     * @param \PhpOffice\PhpSpreadsheet\Writer\IWriter $writer
     *   The writer
     *
     * @return false|string
     *   The writer output as a string or false
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function getOutputAsString(IWriter $writer)
    {
        $filesystem = new Filesystem();
        $tempFilename = $filesystem->tempnam(sys_get_temp_dir(), 'export_');

        // Save to temp file.
        $writer->save($tempFilename);

        $output = file_get_contents($tempFilename);
        $filesystem->remove($tempFilename);

        return $output;
    }

    /**
     * Get output wrapped in a StreamedResponse.
     *
     * @param IWriter $writer
     *
     * @return StreamedResponse
     */
    public function getOutputInStreamedResponse(IWriter $writer): StreamedResponse
    {
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
    }
}

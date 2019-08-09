<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class Manager.
 */
class Manager
{
    /** @var \Symfony\Component\DependencyInjection\Container */
    protected $container;

    /**
     * The configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Constructor.
     */
    public function __construct(ContainerInterface $container, array $configuration = [])
    {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * @return \Kontrolgruppen\CoreBundle\Export\AbstractExport[]
     */
    public function getExports()
    {
        $exports = [];
        if (isset($this->configuration['exports'])) {
            foreach ($this->configuration['exports'] as $service) {
                $export = $this->getExport($service);
                if (null !== $export) {
                    $exports[\get_class($export)] = $export;
                }
            }
        }

        return $exports;
    }

    /**
     * @param $service
     *
     * @return \Kontrolgruppen\CoreBundle\Export\AbstractExport|object|null
     *
     * @throws \Exception
     */
    public function getExport($service)
    {
        $export = $this->container->get($service);

        return $export instanceof AbstractExport ? $export : null;
    }

    /**
     * Run export.
     *
     * @param AbstractExport $export
     * @param string|array   $parameters
     * @param string         $format
     *
     * @return \PhpOffice\PhpSpreadsheet\Writer\IWriter
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function run(AbstractExport $export, $parameters, string $format)
    {
        $parameters = $this->getExportParameters($export, $parameters);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->applyFromArray($styleArray);

        $export->write($parameters, $spreadsheet);
        $type = $this->getWriterType($format);
        $writer = IOFactory::createWriter($spreadsheet, $type);

        return $writer;
    }

    /**
     * Get writer type form format string.
     *
     * @param string $format
     *
     * @return string
     */
    private function getWriterType(string $format)
    {
        $format = ucfirst($format);

        switch ($format) {
            case 'Pdf':
                return 'Mpdf';
            case 'Xls':
            case 'Xlsx':
            case 'Ods':
            case 'Csv':
            case 'Html':
            case 'Tcpdf':
            case 'Dompdf':
            case 'Mpdf':
                return $format;
        }

        throw new \RuntimeException('Invalid format: '.$format);
    }

    /**
     * Parse a string into export parameters.
     *
     * The input must be a space-separated list of name=value pairs, e.g. "start=now end=+1 day"
     *
     * @param string $spec
     *
     * @return array
     */
    public function parseExportParameters(string $spec)
    {
        if (empty($spec)) {
            return [];
        }

        // @TODO: Improve this, e.g. to handle quoted strings.
        $spec = preg_replace('/(?:^|\s)([a-z_]+)=/i', '&\1=', $spec);
        parse_str(preg_replace('/(^|\s)(?<name>[a-z]+)=/i', '&\g{name}=', $spec), $parameters);

        return $parameters;
    }

    /**
     * Get typed export parameters based on the parameters defined by the export.
     *
     * @param AbstractExport $export
     * @param string|array   $parameters
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getExportParameters(AbstractExport $export, $parameters)
    {
        if (\is_string($parameters)) {
            $parameters = $this->parseExportParameters($parameters);
        }
        $exportParameters = $export->getParameters();
        foreach ($exportParameters as $name => &$value) {
            $type = $value['type'] ?? TextType::class;
            $value = $this->convertValue($parameters[$name] ?? null, $type);
        }

        return $exportParameters;
    }

    /**
     * Convert a value to a specific type.
     *
     * @param $value
     * @param $type
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function convertValue($value, $type)
    {
        if (null !== $value) {
            switch ($type) {
                case DateType::class:
                case DateTimeType::class:
                    if (!$value instanceof \DateTime) {
                        $value = new \DateTime($value);
                    }
            }
        }

        return $value;
    }
}

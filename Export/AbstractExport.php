<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\Form\Extension\Core\Type\DateType;

abstract class AbstractExport implements \JsonSerializable
{
    /** @var string */
    protected $title;

    /** @var array */
    protected $parameters;

    /** @var Spreadsheet */
    protected $spreadsheet;

    /** @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet */
    protected $sheet;

    protected $latestRow = 0;

    /**
     * Constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        if (empty($this->title)) {
            throw new \Exception('Export title not defined.');
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get parameters.
     */
    public function getParameters()
    {
        return [
            'startdate' => [
                'type' => DateType::class,
                'type_options' => [
                    'data' => new \DateTime('first day of January'),
                    'label' => 'Start date',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy HH:mm',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                ],
            ],
            'enddate' => [
                'type' => DateType::class,
                'type_options' => [
                    'data' => new \DateTime('last day of December'),
                    'label' => 'End date',
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy HH:mm',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                ],
            ],
        ];
    }

    /**
     * Get value of a single parameter.
     */
    public function getParameterValue($name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Get export filename without extension.
     *
     * @return string
     */
    public function getFilename(array $parameters)
    {
        $filename = $this->getTitle();

        if (isset($parameters['startdate'])) {
            $filename .= '_'.$parameters['startdate']->format('Y-m-d');
        }
        if (isset($parameters['enddate'])) {
            $filename .= '_'.$parameters['enddate']->format('Y-m-d');
        }

        return $filename;
    }

    /**
     * Write report data to a writer.
     */
    public function write(array $parameters, Spreadsheet $spreadsheet)
    {
        $this->parameters = $parameters;
        $this->spreadsheet = $spreadsheet;
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->writeData();
    }

    /**
     * Write actual data.
     */
    abstract public function writeData();

    protected $titleStyle;

    /**
     * Write report title using title format.
     */
    protected function writeTitle($title, $colSpan = 1)
    {
        if (null === $this->titleStyle) {
            $this->titleStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 24,
                ],
            ];
        }

        $this->writeRow([$title], $this->titleStyle);
    }

    protected $headerStyle;

    /**
     * Write header using header format.
     */
    protected function writeHeader(array $data)
    {
        if (null === $this->headerStyle) {
            $this->headerStyle = [
                'font' => [
                    'bold' => true,
                ],
            ];
        }

        $this->writeRow($data, $this->headerStyle);
    }

    protected $footerStyle;

    protected function writeFooter(array $data)
    {
        if (null === $this->footerStyle) {
            $this->footerStyle = [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => [
                        'argb' => 'FFDDDDDD',
                    ],
                ],
            ];
        }

        $this->writeRow($data, $this->footerStyle);
    }

    protected function writeRow(array $row, array $style = null, int $indent = 0)
    {
        ++$this->latestRow;

        foreach ($row as $key => $cell) {
            $columnIndex = $key + 1 + $indent;

            $this->sheet->setCellValueByColumnAndRow($columnIndex, $this->latestRow, $cell);
            $styleCell = $this->sheet->getStyleByColumnAndRow($columnIndex, $this->latestRow);
            if (null !== $style) {
                $styleCell->applyFromArray($style);
            }
        }
    }

    protected function formatBoolean(bool $value)
    {
        return $value ? 'x' : '';
    }

    /**
     * Formats a boolean|null as Ja/Nej/null.
     *
     * @param bool|null $value
     *
     * @return string|null
     */
    protected function formatBooleanYesNoNull(?bool $value)
    {
        return true === $value ? 'Ja' : (false === $value ? 'Nej' : null);
    }

    /**
     * Format a date.
     */
    protected function formatDate(\DateTime $date, $format = 'short')
    {
        $date = (is_a($date, \DateTime::class))
                ? $date
                : new \DateTime(strtotime($date));

        switch ($format) {
            case 'short':
                return $date->format('d-m-Y');
                break;
            case 'long':
                return $date->format('d-m-Y H:i');
                break;
            default:
                return $this->formatDate($date, 'short');
        }
    }

    /**
     * Format a decimal number.
     */
    protected function formatNumber($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '');
    }

    /**
     * Format an amount.
     */
    protected function formatAmount($number, $decimals = 2)
    {
        return $this->formatNumber($number, $decimals);
    }

    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
        ];
    }
}

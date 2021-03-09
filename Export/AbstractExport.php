<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * Class AbstractExport.
 */
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

    protected $titleStyle;
    protected $headerStyle;
    protected $footerStyle;

    protected $latestRow = 0;

    /**
     * AbstractExport constructor.
     *
     * @param CacheItemPoolInterface $cachePhpspreadsheet
     *
     * @throws \Exception
     */
    public function __construct(CacheItemPoolInterface $cachePhpspreadsheet)
    {
        if (empty($this->title)) {
            throw new \Exception('Export title not defined.');
        }

        // Enable PHPSpreadsheet caching
        Settings::setCache(new Psr16Cache($cachePhpspreadsheet));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get parameters.
     *
     * @return array
     *
     * @throws \Exception
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
                    'format' => 'dd-MM-yyyy',
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
                    'format' => 'dd-MM-yyyy',
                    'html5' => false,
                    'attr' => ['class' => 'js-datepicker'],
                ],
            ],
        ];
    }

    /**
     * Get value of a single parameter.
     *
     * @param $name
     *
     * @return mixed|null
     */
    public function getParameterValue($name)
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Get export filename without extension.
     *
     * @param array $parameters
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
     *
     * @param array       $parameters
     * @param Spreadsheet $spreadsheet
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function write(array $parameters, Spreadsheet $spreadsheet)
    {
        $this->parameters = $parameters;
        $this->spreadsheet = $spreadsheet;
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->writeData();
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
        ];
    }

    /**
     * Write actual data.
     */
    abstract public function writeData();

    /**
     * Write report title using title format.
     *
     * @param     $title
     * @param int $colSpan
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

    /**
     * Write header using header format.
     *
     * @param array $data
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

    /**
     * @param array $data
     */
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

    /**
     * @param array      $row
     * @param array|null $style
     * @param int        $indent
     */
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

    /**
     * @param bool $value
     *
     * @return string
     */
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
     * Formats a boolean|null as Medhold/Afvist/null.
     *
     * @param bool|null $value
     *
     * @return string|null
     */
    protected function formatBooleanDecision(?bool $value)
    {
        return true === $value ? 'Medhold' : (false === $value ? 'Afvist' : null);
    }

    /**
     * Format a date.
     *
     * @param \DateTime $date
     * @param string    $format
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function formatDate(\DateTime $date, $format = 'short')
    {
        $date = (is_a($date, \DateTime::class))
                ? $date
                : new \DateTime(strtotime($date));

        switch ($format) {
            case 'short':
                return $date->format('d-m-Y');
            case 'long':
                return $date->format('d-m-Y H:i');
            default:
                return $this->formatDate($date, 'short');
        }
    }

    /**
     * Format a decimal number.
     *
     * @param     $number
     * @param int $decimals
     *
     * @return string
     */
    protected function formatNumber($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '');
    }

    /**
     * Format a decimal number with thousand separators.
     *
     * @param     $number
     * @param int $decimals
     *
     * @return string
     */
    protected function formatNumberWithThousandSeparators($number, $decimals = 2)
    {
        return number_format($number, $decimals, ',', '.');
    }

    /**
     * Format an amount.
     *
     * @param     $number
     * @param int $decimals
     *
     * @return string
     */
    protected function formatAmount($number, $decimals = 2)
    {
        return $this->formatNumber($number, $decimals);
    }
}

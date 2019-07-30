<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

abstract class AbstractExport
{
    /** @var string */
    protected $title;

    /** @var array */
    protected $parameters;

    /** @var WriterInterface */
    protected $writer;

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
                ],
            ],
            'enddate' => [
                'type' => DateType::class,
                'type_options' => [
                    'data' => new \DateTime('last day of December'),
                    'label' => 'End date',
                    'widget' => 'single_text',
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
    public function write(array $parameters, WriterInterface $writer)
    {
        $this->parameters = $parameters;
        $this->writer = $writer;
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
            $this->titleStyle = (new StyleBuilder())
                ->setFontBold()
                ->setFontSize(24)
                ->build();
        }

        $this->writer->addRowWithStyle([$title], $this->titleStyle);
    }

    protected $headerStyle;

    /**
     * Write header using header format.
     */
    protected function writeHeader(array $data)
    {
        if (null === $this->headerStyle) {
            $this->headerStyle = (new StyleBuilder())
                ->setFontBold()
                ->build();
        }

        $this->writeRow($data, $this->headerStyle);
    }

    protected $footerStyle;

    protected function writeFooter(array $data)
    {
        if (null === $this->footerStyle) {
            $this->footerStyle = (new StyleBuilder())
                ->setFontBold()
                ->setBackgroundColor(Color::rgb(0xDD, 0xDD, 0xDD))
                ->build();
        }

        $this->writeRow($data, $this->footerStyle);
    }

    protected function writeRow(array $data, Style $style = null)
    {
        $cells = array_map(function ($value) use ($style) {
            return WriterEntityFactory::createCell($value, $style);
        }, $data);
        $row = WriterEntityFactory::createRow($cells);
        $this->writer->addRow($row);
    }

    protected function formatBoolean(bool $value)
    {
        return $value ? 'x' : '';
    }

    /**
     * Format a date.
     */
    protected function formatDate(\DateTime $date)
    {
        return $date->format('d-m-Y');
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
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\BIExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
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

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * The configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     * @param Filesystem             $filesystem
     * @param array                  $configuration
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, Filesystem $filesystem, array $configuration = [])
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->configuration = $configuration;
    }

    /**
     * @return \Kontrolgruppen\CoreBundle\Export\AbstractExport[]
     *
     * @throws \Exception
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
        try {
            $export = $this->container->get($service);

            return $export instanceof AbstractExport ? $export : null;
        } catch (ServiceNotFoundException $exception) {
            return null;
        }
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
     * Run export and save result to file or dump to stdout.
     *
     * @param AbstractExport $export
     * @param array          $parameters
     * @param string         $format
     * @param string|null    $filename
     *                              Filename to save report to. If null, a filename will be generated.
     *
     * @return BIExport|null
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function save(AbstractExport $export, array $parameters, string $format, string $filename = null)
    {
        if (null === $filename) {
            // BI people seem to like timestamps in filenames …
            $filename = uniqid((new \DateTime())->format('Ymd\THis\-'), true).'-'.$export->getFilename($parameters).'.'.$format;
        }

        if (!empty($this->configuration['export_directory'])
            && !$this->filesystem->isAbsolutePath($filename)) {
            $directory = rtrim($this->configuration['export_directory'], '/');
            $filename = $directory.'/'.$filename;
        }

        $directory = \dirname($filename);
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory);
        }

        $writer = $this->run($export, $parameters, $format);

        $writer->save($filename);

        $export = (new BIExport())
            ->setFilename($filename)
            ->setReport($export);
        $this->entityManager->persist($export);
        $this->entityManager->flush();

        return $export;
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
     * @param BIExport $export
     *
     * @return bool
     */
    public function deleteBIExport(BIExport $export)
    {
        try {
            $this->entityManager->remove($export);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return false;
        }

        return true;
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

        throw new \RuntimeException(sprintf('Invalid format: %s', $format));
    }
}

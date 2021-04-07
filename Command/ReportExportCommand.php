<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Export\Manager;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ReportExportCommand.
 */
class ReportExportCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'kontrolgruppen:report:export';

    /** @var \Kontrolgruppen\CoreBundle\Export\Manager */
    private $exportManager;

    /** @var \App\Repository\UserRepository */
    private $userRepository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var Filesystem */
    private $filesystem;

    /** @var ParameterBagInterface */
    private $parameters;

    /**
     * ReportExportCommand constructor.
     *
     * @param Manager                $exportManager
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     * @param Filesystem             $filesystem
     * @param ParameterBagInterface  $parameters
     */
    public function __construct(Manager $exportManager, UserRepository $userRepository, EntityManagerInterface $entityManager, Filesystem $filesystem, ParameterBagInterface $parameters)
    {
        parent::__construct();
        $this->exportManager = $exportManager;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('export', InputArgument::REQUIRED, 'The export class name')
            ->addArgument(
                'parameters',
                InputArgument::OPTIONAL,
                'The export parameters (space-separated name=value pairs)'
            )
            ->addOption('debug-parameters', null, InputOption::VALUE_NONE, 'Dump parsed parameters to console.')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The export format', 'csv')
            ->addOption('save', null, InputOption::VALUE_NONE, 'Save the export result to file. If --output-filename is not specified a unique filename will be generated.')
            ->addOption('output-filename', null, InputOption::VALUE_REQUIRED, 'Filename to save export result to (implies --save).');
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        $help[] = parent::getHelp();

        $exports = $this->exportManager->getExports();
        $help[] = 'Available exports:';
        $help[] = '';
        foreach ($exports as $export) {
            $help[] = sprintf('  Title: %s', $export->getTitle());
            $help[] = sprintf('  Class name: %s', \get_class($export));
            $help[] = '  Parameters:';
            foreach ($export->getParameters() as $name => $info) {
                $type = $info['type'] ?? TextType::class;
                $type = preg_replace('@^([a-z]+\\\\)+@i', '', $type);
                $help[] = sprintf('    %s: %s', $name, $type);
            }
            $help[] = '';
        }

        return implode(\PHP_EOL, $help);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // @TODO: Run as authenticated user.
        $username = $input->getArgument('username');

        $exportClass = $input->getArgument('export');
        $export = $this->exportManager->getExport($exportClass);
        if (null === $export) {
            throw new RuntimeException(sprintf('Invalid export: %s', $exportClass));
        }
        $parameters = $input->getArgument('parameters');
        $parameters = $this->exportManager->getExportParameters($export, $parameters ?? '');

        if ($input->getOption('debug-parameters')) {
            $output->writeln(json_encode($parameters, \JSON_PRETTY_PRINT));

            return;
        }
        $format = $input->getOption('format');

        $outputFilename = $input->getOption('output-filename');
        $save = $input->getOption('save') || null !== $outputFilename;

        if (!$save) {
            // Dump to stdout.
            $this->exportManager
                ->run($export, $parameters, $format)
                ->save('php://output');
        } else {
            $result = $this->exportManager->save($export, $parameters, $format, $outputFilename);
            if ($output->isVerbose()) {
                $output->writeln(sprintf('Result written to file: %s', $result->getFilename()));
            }
        }
    }
}

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
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReportExportCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:report:export';

    /** @var \Kontrolgruppen\CoreBundle\Export\Manager */
    private $exportManager;

    /** @var \App\Repository\UserRepository */
    private $userRepository;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function __construct(
        Manager $exportManager,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->exportManager = $exportManager;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

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
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The export format', 'csv');
    }

    public function getHelp()
    {
        $help[] = parent::getHelp();

        $exports = $this->exportManager->getExports();
        $help[] = 'Available exports:';
        $help[] = '';
        foreach ($exports as $export) {
            $help[] = sprintf('  Title: %s', $export->getTitle());
            $help[] = sprintf('  Class name: %s', \get_class($export));
            $help[] = sprintf('  Parameters:');
            foreach ($export->getParameters() as $name => $info) {
                $type = $info['type'] ?? TextType::class;
                $type = preg_replace('@^([a-z]+\\\\)+@i', '', $type);
                $help[] = sprintf('    %s: %s', $name, $type);
            }
            $help[] = '';
        }

        return implode(PHP_EOL, $help);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // @TODO: Run as authenticated user.
        $username = $input->getArgument('username');

        $exportClass = $input->getArgument('export');
        $export = $this->exportManager->getExport($exportClass);
        if (null === $export) {
            throw new RuntimeException('Invalid export: '.$exportClass);
        }
        $parameters = $input->getArgument('parameters');
        $format = $input->getOption('format');

        $writer = $this->exportManager->run($export, $parameters, $format);
        $writer->save('php://output');
    }
}

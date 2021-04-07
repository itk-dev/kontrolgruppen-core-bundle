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
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ProcessDeleteCommand.
 */
class ProcessDeleteCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:process:delete';
    protected $processRepository;
    protected $entityManager;

    /**
     * ProcessDeleteCommand constructor.
     *
     * @param ProcessRepository      $processRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ProcessRepository $processRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->processRepository = $processRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}.
     */
    protected function configure()
    {
        $this
            ->setDescription('Deletes the listed processes')
            ->addArgument(
                'Case number',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Case numbers of processes that should be deleted'
            );

        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Only lists the processes that would be deleted. No deletion is performed.'
        );
    }

    /**
     * {@inheritdoc}.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $caseNumbers = $input->getArgument('Case number');

        $processes = $this->processRepository->findBy(['caseNumber' => $caseNumbers]);

        if (empty($processes)) {
            $io->warning('No processes were found based on the provided case numbers. Exiting.');

            return 0;
        }

        $caseNumbersOfFoundProcesses = implode(', ', array_map(function ($process) {
            return $process->getCaseNumber();
        }, $processes));

        if (!$this->isDeleteConfirmed($caseNumbersOfFoundProcesses, $input, $output)) {
            return 0;
        }

        if ($input->getOption('dry-run')) {
            $output->writeln('This is a dry run, so none of the following case numbers will actually be deleted.');
            $output->writeln($caseNumbersOfFoundProcesses);

            return 0;
        }

        foreach ($processes as $process) {
            $this->entityManager->remove($process);
        }

        $this->entityManager->flush();

        $output->writeln('The following processes were deleted:');
        $output->writeln($caseNumbersOfFoundProcesses);

        return 0;
    }

    /**
     * Outputs confirmation question to the console. Returns true if 'y' or false if anything else is answered.
     *
     * @param string          $caseNumbers
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function isDeleteConfirmed(string $caseNumbers, InputInterface $input, OutputInterface $output): bool
    {
        $helper = $this->getHelper('question');

        $questionTemplate = 'The following processes were found: %s'
            .\PHP_EOL
            .'Are you sure you want do delete them? ';

        $questionText = sprintf($questionTemplate, $caseNumbers);
        $question = new ConfirmationQuestion($questionText, false);

        return $helper->ask($input, $output, $question);
    }
}

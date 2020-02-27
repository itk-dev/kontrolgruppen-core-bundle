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

/**
 * Class DeleteCompletedProcessesSinceCommand.
 */
class DeleteCompletedProcessesSinceCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:process:delete-completed-since';
    private $processRepository;
    private $entityManager;
    private $sinceDateFormat = 'd-m-Y';

    /**
     * DeleteCompletedProcessesSinceCommand constructor.
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
    public function configure()
    {
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Only lists the processes that would be deleted. No deletion is performed.'
        );

        // Default since date is three years ago.
        $defaultSinceDate = (new \DateTime())->sub(new \DateInterval('P3Y'));

        $this->addArgument(
            'since',
            InputArgument::OPTIONAL,
            sprintf('Processes completed before this date will be deleted. Format: %s', $this->sinceDateFormat),
            $defaultSinceDate->format($this->sinceDateFormat)
        );
    }

    /**
     * {@inheritdoc}.
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $rawSince = $input->getArgument('since');

        $sinceDate = \DateTime::createFromFormat($this->sinceDateFormat, $rawSince);

        if (!$sinceDate) {
            throw new \RuntimeException('Invalid date format for since argument.');
        }

        $processes = $this->processRepository->findCompletedSince($sinceDate);

        if (!empty($processes)) {
            if (!empty($input->getOption('dry-run'))) {
                $this->performDryRun($output, $processes);
            } else {
                $this->removeProcesses($processes);
            }

            if ($output->isVerbose()) {
                $output->writeln(sprintf('%s process(es) deleted.', \count($processes)));
            }
        } else {
            if ($output->isVerbose()) {
                $output->writeln(
                    sprintf(
                        'No completed processes older than %s found. None deleted.',
                        $sinceDate->format($this->sinceDateFormat)
                    )
                );
            }
        }
    }

    /**
     * Perform dry run.
     *
     * @param OutputInterface $output
     * @param array           $processes
     */
    private function performDryRun(OutputInterface $output, array $processes)
    {
        $output->writeln('This is a dry run, so none of the following case numbers will actually be deleted.');

        foreach ($processes as $process) {
            $output->writeln($process->getCaseNumber());
        }
    }

    /**
     * Remove processes.
     *
     * @param array $processes
     */
    private function removeProcesses(array $processes)
    {
        foreach ($processes as $process) {
            $this->entityManager->remove($process);
        }

        $this->entityManager->flush();
    }
}

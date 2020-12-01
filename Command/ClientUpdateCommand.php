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
use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Repository\ClientRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ClientUpdateCommand.
 */
class ClientUpdateCommand extends Command
{
    protected static $defaultName = 'kontrolgruppen:client:update';
    private $clientRepository;
    private $cprService;
    private $entityManager;
    private $logger;

    /**
     * ClientUpdateCommand constructor.
     *
     * @param ClientRepository       $clientRepository
     * @param CprServiceInterface    $cprService
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface        $logger
     */
    public function __construct(ClientRepository $clientRepository, CprServiceInterface $cprService, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->clientRepository = $clientRepository;
        $this->cprService = $cprService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Updated clients with (if any) new information from the CPR service');
        $this->addOption(
            'dry-run',
            null,
            InputOption::VALUE_NONE,
            'Performs dry run where service calls is performed but nothing is stored in the database.'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isDryRun = !empty($input->getOption('dry-run'));

        $clients = $this->clientRepository->findAll();
        $updatedClients = [];

        foreach ($clients as $client) {
            $cpr = new Cpr($client->getProcess()->getClientCPR());
            try {
                $newInfoAvailable = $this->cprService->isNewClientInfoAvailable(
                    $cpr,
                    $client
                );
            } catch (CprException $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            if (!$newInfoAvailable) {
                continue;
            }

            try {
                $updatedClient = $this->cprService->populateClient($cpr, $client);
            } catch (CprException $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            if (!$isDryRun) {
                $this->entityManager->persist($updatedClient);
            }

            $updatedClients[] = $updatedClient;

            if ($output->isVeryVerbose()) {
                $output->writeln(sprintf('Client with ID: %s was updated.', $client->getId()));
            }
        }

        if ($output->isVerbose()) {
            $output->writeln(sprintf('%s client(s) was updated.', \count($updatedClients)));
        }

        if (!$isDryRun) {
            $this->entityManager->flush();
        }

        return 0;
    }
}

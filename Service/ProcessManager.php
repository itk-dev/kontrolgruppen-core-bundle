<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\Client;
use Kontrolgruppen\CoreBundle\Entity\Conclusion;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Entity\User;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Psr\Log\LoggerInterface;

class ProcessManager
{
    private $processRepository;
    private $entityManager;
    private $lockService;
    private $cprService;
    private $logger;

    /**
     * ProcessManager constructor.
     *
     * @param $processRepository
     */
    public function __construct(
        ProcessRepository $processRepository,
        EntityManagerInterface $entityManager,
        LockService $lockService,
        CprServiceInterface $cprService,
        LoggerInterface $logger
    ) {
        $this->processRepository = $processRepository;
        $this->entityManager = $entityManager;
        $this->lockService = $lockService;
        $this->cprService = $cprService;
        $this->logger = $logger;
    }

    /**
     * Find open processes assigned to user that has not been visited by the user.
     *
     * @return mixed
     */
    public function getUsersUnvisitedProcesses(User $user)
    {
        $query = $this->entityManager->createQuery(
            '
            SELECT p
            FROM Kontrolgruppen\CoreBundle\Entity\Process p
            WHERE p.caseWorker = :caseWorker
            AND NOT EXISTS (
              SELECT l.id
              FROM Gedmo\Loggable\Entity\LogEntry l
              WHERE l.action = \'read\'
              AND l.username = :username
              AND p.id = l.objectId
            )
            '
        )
            ->setParameter('caseWorker', $user)
            ->setParameter('username', $user->getUsername());

        return $query->execute();
    }

    public function markProcessesAsUnvisited(array $unvisitedProcesses, array $processes)
    {
        $unvisitedProcesses = new ArrayCollection($unvisitedProcesses);
        $processes = new ArrayCollection($processes);

        foreach ($processes as $process) {
            if (!$unvisitedProcesses->contains($process)) {
                $process->setVisitedByCaseWorker(true);
            }
        }

        return $processes;
    }

    /**
     * Create new process.
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\Process
     */
    public function newProcess(
        Process $process = null,
        ProcessType $processType = null
    ) {
        if (null === $process) {
            $process = new Process();
            $process->setProcessType($processType);
        }

        $resourceToLock = 'case-number';

        $this->lockService->createLock($resourceToLock);
        // Acquire a blocking lock (cf. https://symfony.com/doc/current/components/lock.html#blocking-locks).
        $this->lockService->acquire($resourceToLock, true);

        if (!$this->lockService->isAcquired($resourceToLock)) {
            throw new \RuntimeException('Could not acquire lock when creating a new Process.');
        }

        $process->setCaseNumber($this->getNewCaseNumber());
        $process->setProcessStatus($this->decideStatusForProcess($process));
        $process->setConclusion($this->createConclusionForProcess($process));
        $process->setClient($this->createClientForProcess($process));

        $this->entityManager->persist($process);
        $this->entityManager->flush();

        $this->lockService->release($resourceToLock);

        return $process;
    }

    private function decideStatusForProcess(Process $process)
    {
        if (empty($process->getCaseWorker())) {
            return $process->getProcessType()->getDefaultProcessStatusOnEmptyCaseWorker();
        }

        return $process->getProcessType()->getDefaultProcessStatus();
    }

    /**
     * Generate a new case number.
     *
     * @return string case number of format YY-XXXX where YY is the year and XXXX an increasing counter
     */
    public function getNewCaseNumber()
    {
        $casesInYear = $this->processRepository->findAllFromYear(date('Y'));

        $highestCaseCounter = 0;

        /** @var Process $process */
        foreach ($casesInYear as $process) {
            $caseCounter = $this->getCaseNumberCounterFromProcess($process);
            if ($caseCounter > $highestCaseCounter) {
                $highestCaseCounter = $caseCounter;
            }
        }

        $caseNumber = str_pad($highestCaseCounter + 1, 5, '0', STR_PAD_LEFT);

        return date('y').'-'.$caseNumber;
    }

    private function getCaseNumberCounterFromProcess(Process $process)
    {
        $positionOfDash = strpos($process->getCaseNumber(), '-');
        $processCounter = (int) substr($process->getCaseNumber(), $positionOfDash + 1);

        return $processCounter;
    }

    private function createConclusionForProcess(Process $process): Conclusion
    {
        $conclusionClass = $process->getProcessType()->getConclusionClass();

        return new $conclusionClass();
    }

    private function createClientForProcess(Process $process): Client
    {
        $client = new Client();

        try {
            $client = $this->cprService->populateClient(new Cpr($process->getClientCPR()), $client);
        } catch (CprException $e) {
            $this->logger->error($e);
        }

        return $client;
    }
}

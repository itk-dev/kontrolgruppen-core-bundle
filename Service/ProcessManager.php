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
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Entity\User;

class ProcessManager
{
    private $processRepository;
    private $entityManager;

    /**
     * ProcessManager constructor.
     *
     * @param $processRepository
     */
    public function __construct(
        ProcessRepository $processRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->processRepository = $processRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Find open processes assigned to user that has not been visited by the user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
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
     * @param \Kontrolgruppen\CoreBundle\Entity\Process|null $process
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

        $process->setCaseNumber($this->getNewCaseNumber());

        $process = $this->enforceUniqueCaseNumber($process);

        $process->setProcessStatus($this->decideStatusForProcess($process));

        $conclusionClass = $process->getProcessType()->getConclusionClass();
        $conclusion = new $conclusionClass();
        $process->setConclusion($conclusion);

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
        $caseNumber = str_pad(\count($casesInYear) + 1, 5, '0', STR_PAD_LEFT);

        return date('y').'-'.$caseNumber;
    }

    private function enforceUniqueCaseNumber(Process $process): Process
    {
        $duplicateProcess = $this->processRepository->findBy(
            ['caseNumber' => $process->getCaseNumber()]
        );

        if (empty($duplicateProcess)) {
            return $process;
        }

        $process->setCaseNumber($this->getNewCaseNumber());

        return $this->enforceUniqueCaseNumber($process);
    }
}

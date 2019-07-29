<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
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
    public function __construct(ProcessRepository $processRepository, EntityManagerInterface $entityManager)
    {
        $this->processRepository = $processRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Find open processes assigned to user that has not been visited by the user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     * @return mixed
     */
    public function getUsersUnvisitedProcesses(User $user)
    {
        $processes = $this->processRepository->findUserOpenProcessIds($user);
        $ids = $titles = array_map(function($e) {
            return $e->getId();
        }, $processes);

        $qb = $this->entityManager->getRepository(LogEntry::class)->createQueryBuilder('e');
        $qb->select('e.objectId');
        $qb->where('e.action = \'read\'');
        $qb->andWhere($qb->expr()->in('e.objectId', ':ids'));
        $qb->setParameter('ids', $ids);
        $qb->andWhere($qb->expr()->eq('e.username', ':username'));
        $qb->setParameter('username', $user->getUsername());
        $readProcessIds = $qb->getQuery()->getScalarResult();

        $readProcessIds = array_column($readProcessIds, 'objectId');

        $result = array_reduce($processes, function ($carry, $process) use ($readProcessIds) {
            if (!in_array($process->getId(), $readProcessIds)) {
                $carry[] = $process;
            }

            return $carry;
        }, []);

        return $result;
    }

    /**
     * Create new process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process|null $process
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\Process
     */
    public function newProcess(Process $process = null, ProcessType $processType = null)
    {
        if (null === $process) {
            $process = new Process();
            $process->setProcessType($processType);
        }
        $process->setCaseNumber($this->getNewCaseNumber());

        $conclusionClass = $process->getProcessType()->getConclusionClass();
        $conclusion = new $conclusionClass();
        $process->setConclusion($conclusion);

        return $process;
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
}

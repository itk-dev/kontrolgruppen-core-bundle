<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;

/**
 * @method ProcessStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessStatus[]    findAll()
 * @method ProcessStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessStatusRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessStatus::class);
    }

    /**
     * Get process statuses that are available for the process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process
     *
     * @return mixed
     *   The process statuses that are available for the process
     */
    public function getAvailableForProcess(Process $process)
    {
        $qb = $this->createQueryBuilder('p')
            ->where(':process MEMBER OF p.processes')
            ->setParameter('process', $process)
            ->getQuery();

        return $qb->execute();
    }
}

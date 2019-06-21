<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProcessLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessLogEntry[]    findAll()
 * @method ProcessLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessLogEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProcessLogEntry::class);
    }

    public function getLatestEntries(Process $process, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('processLogEntry', 'processLogEntry.id');
        $qb
            ->select(['processLogEntry', 'logEntry'])
            ->where('processLogEntry.process = :process')
            ->setParameter('process', $process)
            ->innerJoin('processLogEntry.logEntry', 'logEntry')
            ->orderBy('logEntry.loggedAt', 'DESC');

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getArrayResult();
    }
}

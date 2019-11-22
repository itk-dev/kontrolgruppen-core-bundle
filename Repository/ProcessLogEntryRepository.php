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
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProcessLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessLogEntry[]    findAll()
 * @method ProcessLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessLogEntryRepository extends ServiceEntityRepository
{
    protected $paginator;

    public function __construct(
        RegistryInterface $registry,
        PaginatorInterface $paginator
    ) {
        parent::__construct($registry, ProcessLogEntry::class);
        $this->paginator = $paginator;
    }

    public function getLatestEntriesPaginated(
        Process $process,
        int $page = 1,
        int $limit = 20
    ) {
        return $this->paginator->paginate(
            $this->getLatestEntriesQuery($process),
            $page,
            $limit
        );
    }

    public function getLatestEntriesByLevel(
        $level = ProcessLogEntryLevelEnumType::NOTICE,
        $limit = 5,
        Process $process = null
    ) {
        $qb = $this->createQueryBuilder(
            'processLogEntry',
            'processLogEntry.id'
        );
        $qb
            ->select(['processLogEntry', 'logEntry', 'process'])
            ->where('processLogEntry.level = :level')
            ->setParameter('level', $level)
            ->innerJoin('processLogEntry.logEntry', 'logEntry')
            ->innerJoin('processLogEntry.process', 'process')
            ->orderBy('logEntry.loggedAt', 'DESC');

        if ($process) {
            $qb
                ->andWhere('processLogEntry.process = :process')
                ->setParameter('process', $process);
        }

        $qb->setMaxResults($limit);

        $query = $qb->getQuery();

        return $query->getArrayResult();
    }

    public function getLatestLogEntries(Process $process, $level)
    {
        $qb = $this->createQueryBuilder(
            'processLogEntry',
            'processLogEntry.id'
        );
        $qb
            ->select(['processLogEntry', 'logEntry'])
            ->where('processLogEntry.process = :process')
            ->setParameter('process', $process)
            ->andWhere('processLogEntry.level = :level')
            ->setParameter('level', $level)
            ->innerJoin('processLogEntry.logEntry', 'logEntry')
            ->orderBy('logEntry.loggedAt', 'DESC');

        return $qb->getQuery();
    }

    protected function getLatestEntriesQuery(Process $process)
    {
        $qb = $this->createQueryBuilder(
            'processLogEntry',
            'processLogEntry.id'
        );
        $qb
            ->select(['processLogEntry', 'logEntry'])
            ->where('processLogEntry.process = :process')
            ->setParameter('process', $process)
            ->innerJoin('processLogEntry.logEntry', 'logEntry')
            ->orderBy('logEntry.loggedAt', 'DESC');

        return $qb->getQuery();
    }

    public function getAllLogEntries(Process $process)
    {
        return $this->getLatestEntriesQuery($process)->getResult();
    }
}

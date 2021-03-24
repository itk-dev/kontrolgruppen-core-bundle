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
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Traversable;

/**
 * @method ProcessLogEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessLogEntry[]    findAll()
 * @method ProcessLogEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessLogEntryRepository extends ServiceEntityRepository
{
    protected $paginator;

    /**
     * ProcessLogEntryRepository constructor.
     *
     * @param \Doctrine\Persistence\ManagerRegistry   $registry
     *   The registry
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     *   The paginator
     */
    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, ProcessLogEntry::class);
        $this->paginator = $paginator;
    }

    /**
     * Get the latest entries, paginated.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process the logs belong to
     * @param int                                       $page
     *   The pagination page
     * @param int                                       $limit
     *   The limit on number of results
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     *   The pagination result
     */
    public function getLatestEntriesPaginated(Process $process, int $page = 1, int $limit = 20)
    {
        return $this->paginator->paginate(
            $this->getLatestEntriesQuery($process),
            $page,
            $limit
        );
    }

    /**
     * Get the latest entries by log level.
     *
     * @param string                                         $level
     *   The log level
     * @param int                                            $limit
     *   The limit on number of results
     * @param \Kontrolgruppen\CoreBundle\Entity\Process|null $process
     *   The process the logs belong to
     *
     * @return array
     *   The latest log entries
     */
    public function getLatestEntriesByLevel($level = ProcessLogEntryLevelEnumType::NOTICE, $limit = 5, Process $process = null)
    {
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

    /**
     * Get the latest log entries.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process the logs belong to
     * @param                                           $level
     *   The log level
     *
     * @return \Doctrine\ORM\Query
     *   The query
     */
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

    /**
     * Get all log entries.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process the logs belong to
     *
     * @return mixed
     *   The result
     */
    public function getAllLogEntries(Process $process)
    {
        return $this->getLatestEntriesQuery($process)->getResult();
    }

    /**
     * Get all log entries utilizing batch processing.
     *
     * @param Process $process
     * @param int     $batchSize
     *
     * @return Traversable
     */
    public function getAllLogEntriesBatchProcessed(Process $process, $batchSize = 100): Traversable
    {
        return SimpleBatchIteratorAggregate::fromQuery(
            $this->getLatestEntriesQuery($process),
            $batchSize
        );
    }

    /**
     * Get the latest entries, as query.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *   The process the logs belong to
     *
     * @return \Doctrine\ORM\Query
     *   The query
     */
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
}

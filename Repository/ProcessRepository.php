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
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\Service;
use Traversable;

/**
 * @method Process|null find($id, $lockMode = null, $lockVersion = null)
 * @method Process|null findOneBy(array $criteria, array $orderBy = null)
 * @method Process[]    findAll()
 * @method Process[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Process::class);
    }

    /**
     * Get all processes from a given year.
     *
     * @param $year
     *   The creation year
     *
     * @return mixed
     *   The processes
     *
     * @throws \Exception
     *   Datetime exception
     */
    public function findAllFromYear($year)
    {
        $from = new \DateTime($year.'-01-01 00:00:00');
        $to = new \DateTime($year.'-12-31 23:59:59');

        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime $since
     *
     * @return Process[]
     */
    public function findCompletedSince(\DateTime $since): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->where('p.completedAt <= :since')
            ->setParameter('since', $since);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Service $service
     *
     * @return Process[]
     */
    public function findCompletedByService(Service $service)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->where('p.service = :service')
            ->setParameter(':service', $service)
            ->andWhere('p.completedAt IS NOT NULL')
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Process[]
     */
    public function findCompletedWithNoStatus(): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->where('p.completedAt IS NOT NULL')
            ->andWhere('p.processStatus IS NULL')
        ;

        return $queryBuilder
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Find all processes utilizing batch processing.
     *
     * @param int $batchSize
     *
     * @return Traversable
     */
    public function findAllBatchProcessed(int $batchSize = 100): Traversable
    {
        return SimpleBatchIteratorAggregate::fromQuery(
            $this->createQueryBuilder('p')->getQuery(),
            $batchSize
        );
    }
}

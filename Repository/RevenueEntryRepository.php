<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\RevenueEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RevenueEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method RevenueEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method RevenueEntry[]    findAll()
 * @method RevenueEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RevenueEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevenueEntry::class);
    }

    // /**
    //  * @return RevenueEntry[] Returns an array of RevenueEntry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RevenueEntry
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

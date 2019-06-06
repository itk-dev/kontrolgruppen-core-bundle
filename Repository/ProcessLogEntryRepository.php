<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use App\Entity\ProcessLogEntry;
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

    // /**
    //  * @return ProcessLogEntry[] Returns an array of ProcessLogEntry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProcessLogEntry
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

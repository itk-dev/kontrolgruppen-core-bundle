<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EconomyEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method EconomyEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method EconomyEntry[]    findAll()
 * @method EconomyEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EconomyEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EconomyEntry::class);
    }

    // /**
    //  * @return EconomyEntry[] Returns an array of EconomyEntry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EconomyEntry
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\LockedNetValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LockedNetValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method LockedNetValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method LockedNetValue[]    findAll()
 * @method LockedNetValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LockedNetValueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LockedNetValue::class);
    }
}

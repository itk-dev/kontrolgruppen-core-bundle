<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\IncomeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method IncomeType|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncomeType|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncomeType[]    findAll()
 * @method IncomeType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, IncomeType::class);
    }
}

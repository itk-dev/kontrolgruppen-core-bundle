<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\CaseType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CaseType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaseType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaseType[]    findAll()
 * @method CaseType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaseTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CaseType::class);
    }
}

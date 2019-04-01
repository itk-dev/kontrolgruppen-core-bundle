<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProcessStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessStatus[]    findAll()
 * @method ProcessStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProcessStatus::class);
    }
}

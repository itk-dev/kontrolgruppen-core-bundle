<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\Conclusion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Conclusion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conclusion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conclusion[]    findAll()
 * @method Conclusion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConclusionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Conclusion::class);
    }
}

<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method QuickLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuickLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuickLink[]    findAll()
 * @method QuickLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuickLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, QuickLink::class);
    }
}
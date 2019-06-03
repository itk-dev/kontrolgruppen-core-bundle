<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

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

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $orderBy = $orderBy === null ? ['weight' => 'ASC'] : $orderBy;
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}

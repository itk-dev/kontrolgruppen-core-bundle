<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\Entity\QuickLink;

/**
 * @method QuickLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuickLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuickLink[]    findAll()
 * @method QuickLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuickLinkRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuickLink::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $orderBy = null === $orderBy ? ['weight' => 'ASC'] : $orderBy;

        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }
}

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
use Doctrine\Common\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\Entity\ProcessGroup;

/**
 * @method ProcessGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessGroup[]    findAll()
 * @method ProcessGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessGroupRepository extends ServiceEntityRepository
{
    /**
     * ProcessGroupRepository constructor
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessGroup::class);
    }
}

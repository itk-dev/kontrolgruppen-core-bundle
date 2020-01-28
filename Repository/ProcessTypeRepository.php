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
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProcessType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessType[]    findAll()
 * @method ProcessType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessType::class);
    }
}

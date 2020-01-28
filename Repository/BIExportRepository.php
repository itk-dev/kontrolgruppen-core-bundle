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
use Kontrolgruppen\CoreBundle\Entity\BIExport;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BIExport|null find($id, $lockMode = null, $lockVersion = null)
 * @method BIExport|null findOneBy(array $criteria, array $orderBy = null)
 * @method BIExport[]    findAll()
 * @method BIExport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BIExportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BIExport::class);
    }
}

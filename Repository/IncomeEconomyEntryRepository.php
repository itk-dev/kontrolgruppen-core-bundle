<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use App\Entity\Kontrolgruppen\CoreBundle\Entity\IncomeEconomyEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IncomeEconomyEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncomeEconomyEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncomeEconomyEntry[]    findAll()
 * @method IncomeEconomyEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeEconomyEntryRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncomeEconomyEntry::class);
    }
}

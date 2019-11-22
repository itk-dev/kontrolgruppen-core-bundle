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
use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EconomyEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method EconomyEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method EconomyEntry[]    findAll()
 * @method EconomyEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EconomyEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EconomyEntry::class);
    }
}

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
use Kontrolgruppen\CoreBundle\Entity\Conclusion;

/**
 * @method Conclusion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conclusion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conclusion[]    findAll()
 * @method Conclusion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConclusionRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conclusion::class);
    }
}

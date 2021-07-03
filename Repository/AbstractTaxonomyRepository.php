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

/**
 * Base class for taxonomy repositories.
 */
abstract class AbstractTaxonomyRepository extends ServiceEntityRepository
{
    /**
     * The taxonomy class for this repository.
     *
     * @var string
     */
    protected static $taxonomyClass;

    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::$taxonomyClass);
    }

    /**
     * Find reasons by client type.
     *
     * @param string|null $clientType The client type
     *
     * @return mixed
     */
    public function findByClientType(string $clientType = null)
    {
        $queryBuilder = $this
            ->createQueryBuilder('t', 't.id')
            ->where('t.clientTypes IS NULL')
            ->orWhere('t.clientTypes LIKE :clientType')
            ->setParameter('clientType', '%'.json_encode($clientType, \JSON_THROW_ON_ERROR).'%')
            ->getQuery();

        return $queryBuilder->getResult();
    }
}

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
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;

/**
 * @method ProcessClientPerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessClientPerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessClientPerson[]    findAll()
 * @method ProcessClientPerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessClientPersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessClientPerson::class);
    }
}

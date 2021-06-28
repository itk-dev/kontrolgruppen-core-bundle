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
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;

/**
 * @method ProcessClientCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessClientCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessClientCompany[]    findAll()
 * @method ProcessClientCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessClientCompanyRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessClientCompany::class);
    }
}

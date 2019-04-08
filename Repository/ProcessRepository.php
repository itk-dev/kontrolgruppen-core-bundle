<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Process|null find($id, $lockMode = null, $lockVersion = null)
 * @method Process|null findOneBy(array $criteria, array $orderBy = null)
 * @method Process[]    findAll()
 * @method Process[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Process::class);
    }

    public function findAllFromYear($year)
    {
        $from = new \DateTime($year.'-01-01 00:00:00');
        $to = new \DateTime($year.'-12-31 23:59:59');

        $qb = $this->createQueryBuilder('e');
        $qb
            ->andWhere('e.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}

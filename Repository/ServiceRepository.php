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
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Entity\Service;

/**
 * @method Service|null find($id, $lockMode = null, $lockVersion = null)
 * @method Service|null findOneBy(array $criteria, array $orderBy = null)
 * @method Service[]    findAll()
 * @method Service[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }

    public function getByProcessType(ProcessType $processType)
    {
        $qb = $this->createQueryBuilder('service', 'service.id')
            ->where(':processType MEMBER OF service.processTypes')
            ->setParameter('processType', $processType)
            ->getQuery();

        return $qb->getResult();
    }

    public function getByProcess(Process $process)
    {
        $qb = $this->createQueryBuilder('service', 'service.id')
            ->join('\Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry', 'e', 'WITH', 'e.service = service')
            ->where('e.process = :process')
            ->setParameter('process', $process)
            ->getQuery();

        return $qb->getResult();
    }
}

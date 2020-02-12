<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;

class ProcessSearchService
{
    private $authorizationChecker;
    private $paginator;
    private $processRepository;

    public function __construct(PaginatorInterface $paginator, ProcessRepository $processRepository)
    {
        $this->paginator = $paginator;
        $this->processRepository = $processRepository;
    }

    public function all($search, int $page = 1, $limit = 50): PaginationInterface
    {
        $qb = $this->getQueryBuilder();

        $qb->where('e.caseNumber LIKE :search');
        $qb->orWhere('e.clientCPR LIKE :search');
        $qb->orWhere('client.firstName LIKE :search');
        $qb->orWhere('client.lastName LIKE :search');
        $qb->orWhere(
            $qb->expr()->concat(
                $qb->expr()->concat('client.firstName', $qb->expr()->literal(' ')),
                'client.lastName'
            ).'LIKE :search'
        );
        $qb->orWhere('client.telephone LIKE :search');
        $qb->orWhere('client.address LIKE :search');
        $qb->orWhere('caseWorker.username LIKE :search');
        $qb->setParameter(':search', '%'.$search.'%');

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    public function single($search, int $page = 1, $limit = 50): PaginationInterface
    {
        $qb = $this->getQueryBuilder();

        $qb->where('e.caseNumber = :search');
        $qb->orWhere('e.clientCPR = :search');
        $qb->orWhere('client.telephone = :search');
        $qb->orWhere('client.address = :search');
        $qb->orWhere(
            $qb->expr()->concat(
                $qb->expr()->concat('client.firstName', $qb->expr()->literal(' ')),
                'client.lastName'
            ).'= :search'
        );
        $qb->setParameter(':search', $search);

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $qb = $this->processRepository->createQueryBuilder('e');
        $qb->leftJoin('e.client', 'client');
        $qb->addSelect('client');
        $qb->leftJoin('e.caseWorker', 'caseWorker');
        $qb->addSelect('partial caseWorker.{id,username}');

        // Add sortable fields.
        $qb->leftJoin('e.channel', 'channel');
        $qb->addSelect('partial channel.{id,name}');

        $qb->leftJoin('e.service', 'service');
        $qb->addSelect('partial service.{id,name}');

        $qb->leftJoin('e.processType', 'processType');
        $qb->addSelect('partial processType.{id,name}');

        $qb->leftJoin('e.processStatus', 'processStatus');
        $qb->addSelect('partial processStatus.{id,name}');

        return $qb;
    }
}

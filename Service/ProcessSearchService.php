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

        $fieldMatches = $this->getFieldMatches($search);

        if (count($fieldMatches) > 0) {
            $qb = $this->applyFieldSearch($qb, $fieldMatches);
        }

        $qb->orWhere('e.caseNumber LIKE :search');
        $qb->orWhere('e.clientCPR LIKE :search');
        $qb->orWhere('client.telephone LIKE :search');
        $qb->orWhere('client.firstName LIKE :search');
        $qb->orWhere('client.lastName LIKE :search');
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

        $fieldMatches = $this->getFieldMatches($search);

        if (count($fieldMatches) > 0) {
            $qb = $this->applyFieldSearch($qb, $fieldMatches);
        }
        else {
            $qb->orWhere('client.address = :search');
            $qb->orWhere(
                $qb->expr()->concat(
                    $qb->expr()->concat(
                        'client.firstName',
                        $qb->expr()->literal(' ')
                    ),
                    'client.lastName'
                ).'= :search'
            );
            $qb->setParameter(':search', $search);
        }

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }

    private function applyFieldSearch(QueryBuilder $queryBuilder, array $matches): QueryBuilder
    {
        if (isset($matches['caseNumber'])) {
            $queryBuilder->orWhere('e.caseNumber = :search_case_number_alternative');
            $queryBuilder->setParameter(':search_case_number_alternative', $matches['caseNumber']);
        }
        if (isset($matches['cpr'])) {
            $queryBuilder->orWhere('e.clientCPR = :search_cpr_alternative');
            $queryBuilder->setParameter(':search_cpr_alternative', $matches['cpr']);
        }
        if (isset($matches['telephone'])) {
            $queryBuilder->orWhere('client.telephone = :search_telephone_alternative');
            $queryBuilder->setParameter(':search_telephone_alternative', $matches['telephone']);
        }

        return $queryBuilder;
    }

    /**
     * Get possible matches between fields and the search.
     *
     * @param string $search
     *   The search string.
     * @return array
     */
    private function getFieldMatches(string $search): array
    {
        preg_match('/^\d{2}-?\d{5}$/', $search, $possibleCaseNumberMatches);
        preg_match('/^\d{6}-?\d{4}$/', $search, $possibleCPRMatches);
        preg_match('/^\d{8}$/', $search, $possiblePhoneNumberMatches);

        $result = [];
        if (count($possibleCaseNumberMatches) === 1) {
            $match = $possibleCaseNumberMatches[0];

            // Add '-' if missing.
            if (strlen($match) === 7) {
                $match = substr($match, 0, 2).'-'.substr($match, 2, 5);
            }

            $result['caseNumber'] = $match;
        }
        if (count($possibleCPRMatches) === 1) {
            $match = $possibleCPRMatches[0];

            // Add '-' if missing.
            if (strlen($match) === 10) {
                $match = substr($match, 0, 6).'-'.substr($match, 6, 4);
            }

            $result['cpr'] = $match;
        }
        if (count($possiblePhoneNumberMatches) === 1) {
            $result['telephone'] = $possiblePhoneNumberMatches[0];
        }

        return $result;
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

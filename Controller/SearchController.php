<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class SearchController.
 *
 * @Route("/search")
 */
class SearchController extends BaseController
{
    /**
     * @Route("/", name="search_index")
     */
    public function index(Request $request, ProcessRepository $processRepository, PaginatorInterface $paginator)
    {
        $search = $request->query->get('search');

        $qb = $processRepository->createQueryBuilder('e');
        $qb->leftJoin('e.client', 'client');
        $qb->addSelect('client');
        $qb->leftJoin('e.caseWorker', 'caseWorker');
        $qb->addSelect('partial caseWorker.{id,username}');
        $qb->where('e.caseNumber LIKE :search');
        $qb->orWhere('e.clientCPR LIKE :search');
        $qb->orWhere('client.firstName LIKE :search');
        $qb->orWhere('client.lastName LIKE :search');
        $qb->orWhere('client.telephone LIKE :search');
        $qb->orWhere('client.address LIKE :search');
        $qb->orWhere('caseWorker.username LIKE :search');
        $qb->setParameter(':search', '%'.$search.'%');

        // Add sortable fields.
        $qb->leftJoin('e.channel', 'channel');
        $qb->addSelect('partial channel.{id,name}');

        $qb->leftJoin('e.service', 'service');
        $qb->addSelect('partial service.{id,name}');

        $qb->leftJoin('e.processType', 'processType');
        $qb->addSelect('partial processType.{id,name}');

        $qb->leftJoin('e.processStatus', 'processStatus');
        $qb->addSelect('partial processStatus.{id,name}');

        $query = $qb->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            50
        );
        $pagination->setCustomParameters([
          'align' => 'center',
          'size' => 'small',
          'style' => 'bottom',
        ]);

        return $this->render(
            '@KontrolgruppenCore/search/index.html.twig',
            [
                'pagination' => $pagination,
                'search' => $search,
            ]
        );
    }
}

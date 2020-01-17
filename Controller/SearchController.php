<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Service\ProcessSearchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
    public function index(Request $request, ProcessRepository $processRepository, PaginatorInterface $paginator, ProcessSearchService $processSearchService)
    {
        $search = $request->query->get('search');

        if (!$this->isGranted('edit', new Process())) {
            return $this->redirectToRoute('search_external', ['search' => $search]);
        }

        $pagination = $processSearchService->all(
            $search,
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

    /**
     * @Route("/external", name="search_external")
     */
    public function external(Request $request, ProcessRepository $processRepository, PaginatorInterface $paginator, ProcessSearchService $processSearchService)
    {
        $search = $request->query->get('search');
        $pagination = $processSearchService->single(
            $search,
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

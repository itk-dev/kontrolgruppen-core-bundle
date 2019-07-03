<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/log")
 */
class ProcessLogController extends BaseController
{
    /**
     * @Route("/", name="process_log_index", methods={"GET","POST"})
     */
    public function index(Request $request, Process $process): Response
    {
        // Latest Log entries
        $logEntriesPagination = $this->getDoctrine()->getRepository(
            ProcessLogEntry::class
        )->getLatestEntriesPaginated(
            $process,
            $request->query->get('page', 1),
            20
        );

        return $this->render('@KontrolgruppenCoreBundle/process_log/index.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process' => $process,
            'logEntriesPagination' => $logEntriesPagination,
        ]);
    }
}

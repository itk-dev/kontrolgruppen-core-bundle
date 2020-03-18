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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/relation")
 */
class ProcessRelationController extends BaseController
{
    /**
     * @Route("/", name="process_relation_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, Process $process): Response
    {
        return $this->render('@KontrolgruppenCoreBundle/process_relation/index.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process' => $process,
            'relations' => $this->getRelatedProcesses($process),
        ]);
    }

    /**
     * Get related processes for a process.
     *
     * @param Process $process
     *
     * @return array
     */
    private function getRelatedProcesses(Process $process): array
    {
        $relations = [];
        foreach ($process->getProcessGroups() as $processGroup) {
            foreach ($processGroup->getProcesses() as $relatedProcess) {
                // We don't want to show the lookup process amongst the list of related processes.
                if ($process->getId() === $relatedProcess->getId()) {
                    continue;
                }

                $relations[] = [
                    'isPrimary' => $relatedProcess->getId() === $processGroup->getPrimaryProcess()->getId(),
                    'process' => $relatedProcess,
                ];
            }
        }

        return $relations;
    }
}

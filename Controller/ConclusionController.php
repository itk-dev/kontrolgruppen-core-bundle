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
use Kontrolgruppen\CoreBundle\Event\GetConclusionTemplateEvent;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/conclusion")
 */
class ConclusionController extends BaseController
{
    /**
     * @Route("/", name="conclusion_show", methods={"GET","POST"})
     *
     * @param Request                  $request
     * @param Process                  $process
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Process $process, EventDispatcherInterface $dispatcher): Response
    {
        $conclusion = $process->getConclusion();

        // Create conclusion for the given process, if none exist.
        if (null === $conclusion) {
            $conclusionType = $process->getProcessType()->getConclusionClass();
            $conclusion = new $conclusionType();

            $this->getDoctrine()->getManager()->persist($conclusion);

            $process->setConclusion($conclusion);

            $this->getDoctrine()->getManager()->flush();
        }

        // Get template from event.
        $event = new GetConclusionTemplateEvent(\get_class($conclusion), 'show');
        $template = $dispatcher->dispatch(GetConclusionTemplateEvent::NAME, $event)->getTemplate();

        return $this->render($template, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'conclusion' => $process->getConclusion(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/edit", name="conclusion_edit", methods={"GET","POST"})
     *
     * @param Request                  $request
     * @param Process                  $process
     * @param ConclusionService        $conclusionService
     * @param EventDispatcherInterface $dispatcher
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Process $process, ConclusionService $conclusionService, EventDispatcherInterface $dispatcher): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $conclusion = $process->getConclusion();
        $options = [];

        // Disable the form if the process is completed.
        if (null !== $process->getCompletedAt()) {
            $options['disabled'] = true;
        }

        $form = $this->createForm($conclusionService->getEntityFormType($conclusion), $conclusion, $options);

        // Only handle form if the process is not completed.
        if (null === $process->getCompletedAt()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('conclusion_show', [
                    'process' => $process->getId(),
                ]);
            }
        }

        // Get template from event.
        $event = new GetConclusionTemplateEvent(\get_class($conclusion), 'edit');
        $template = $dispatcher->dispatch(GetConclusionTemplateEvent::NAME, $event)->getTemplate();

        return $this->render($template, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'conclusion' => $conclusion,
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'form' => $form->createView(),
            'process' => $process,
        ]);
    }
}

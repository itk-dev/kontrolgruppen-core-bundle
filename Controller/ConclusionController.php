<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Event\GetConclusionTemplateEvent;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * @Route("/process/{process}/conclusion")
 */
class ConclusionController extends BaseController
{
    /**
     * @Route("/", name="conclusion_show", methods={"GET"})
     */
    public function show(Request $request, Process $process, EventDispatcherInterface $dispatcher): Response
    {
        $conclusion = $process->getConclusion();

        if (null === $conclusion) {
            $conclusionType = $process->getProcessType()->getConclusionClass();
            $conclusion = new $conclusionType();

            $this->getDoctrine()->getManager()->persist($conclusion);

            $process->setConclusion($conclusion);

            $this->getDoctrine()->getManager()->flush();
        }

        $event = new GetConclusionTemplateEvent(\get_class($conclusion), 'show');
        $template = $dispatcher->dispatch(GetConclusionTemplateEvent::NAME, $event)->getTemplate();

        return $this->render($template, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'conclusion' => $process->getConclusion(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/edit", name="conclusion_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process, ConclusionService $conclusionService, EventDispatcherInterface $dispatcher): Response
    {
        $conclusion = $process->getConclusion();

        $form = $this->createForm($conclusionService->getEntityFormType($conclusion), $conclusion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conclusion_show', [
                'process' => $process->getId(),
            ]);
        }

        $event = new GetConclusionTemplateEvent(\get_class($conclusion), 'edit');
        $template = $dispatcher->dispatch(GetConclusionTemplateEvent::NAME, $event)->getTemplate();

        return $this->render($template, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'conclusion' => $conclusion,
            'form' => $form->createView(),
            'process' => $process,
        ]);
    }
}

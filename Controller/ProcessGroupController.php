<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessGroup;
use Kontrolgruppen\CoreBundle\Form\ProcessGroupType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/group")
 */
class ProcessGroupController extends BaseController
{
    /**
     * @Route("/", name="process_group_index", methods={"GET"})
     */
    public function index(Request $request, Process $process): Response
    {
        return $this->render('@KontrolgruppenCore/process_group/index.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/new", name="process_group_new", methods={"GET","POST"})
     */
    public function new(Request $request, Process $process): Response
    {
        $processGroup = new ProcessGroup();
        $processGroup->setPrimaryProcess($process);
        $processGroup->addProcess($process);
        $form = $this->createForm(ProcessGroupType::class, $processGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($processGroup);
            $entityManager->flush();

            return $this->redirectToRoute('process_group_index', ['process' => $process]);
        }

        return $this->render('@KontrolgruppenCore/process_group/new.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process_group' => $processGroup,
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_group_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProcessGroup $processGroup, Process $process): Response
    {
        $form = $this->createForm(ProcessGroupType::class, $processGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('process_group_index', ['process' => $process]);
        }

        return $this->render('@KontrolgruppenCore/process_group/edit.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu(
                $request->getPathInfo(),
                $process
            ),
            'process_group' => $processGroup,
            'form' => $form->createView(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/{id}", name="process_group_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProcessGroup $processGroup, Process $process): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processGroup->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($processGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_group_index', ['process' => $process]);
    }
}

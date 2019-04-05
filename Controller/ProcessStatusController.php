<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Form\ProcessStatusType;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process_status")
 */
class ProcessStatusController extends BaseController
{
    /**
     * @Route("/", name="process_status_index", methods={"GET"})
     */
    public function index(ProcessStatusRepository $processStatusRepository): Response
    {
        return $this->render('@KontrolgruppenCore/process_status/index.html.twig', [
            'process_statuses' => $processStatusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="process_status_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $processStatus = new ProcessStatus();
        $form = $this->createForm(ProcessStatusType::class, $processStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($processStatus);
            $entityManager->flush();

            return $this->redirectToRoute('process_status_index');
        }

        return $this->render('@KontrolgruppenCore/process_status/new.html.twig', [
            'process_status' => $processStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_status_show", methods={"GET"})
     */
    public function show(ProcessStatus $processStatus): Response
    {
        return $this->render('@KontrolgruppenCore/process_status/show.html.twig', [
            'process_status' => $processStatus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_status_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProcessStatus $processStatus): Response
    {
        $form = $this->createForm(ProcessStatusType::class, $processStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('process_status_index', [
                'id' => $processStatus->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/process_status/edit.html.twig', [
            'process_status' => $processStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_status_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProcessStatus $processStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($processStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_status_index');
    }
}

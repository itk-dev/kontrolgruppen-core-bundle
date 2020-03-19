<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Form\ProcessStatusType;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/process_status")
 */
class ProcessStatusController extends BaseController
{
    /**
     * @Route("/", name="process_status_index", methods={"GET"})
     *
     * @param Request                 $request
     * @param ProcessStatusRepository $processStatusRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ProcessStatusRepository $processStatusRepository): Response
    {
        return $this->render('@KontrolgruppenCore/process_status/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_statuses' => $processStatusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="process_status_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_status' => $processStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_status_show", methods={"GET"})
     *
     * @param Request       $request
     * @param ProcessStatus $processStatus
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, ProcessStatus $processStatus): Response
    {
        return $this->render('@KontrolgruppenCore/process_status/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_status' => $processStatus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_status_edit", methods={"GET","POST"})
     *
     * @param Request       $request
     * @param ProcessStatus $processStatus
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_status' => $processStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_status_delete", methods={"DELETE"})
     *
     * @param Request       $request
     * @param ProcessStatus $processStatus
     *
     * @return Response
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

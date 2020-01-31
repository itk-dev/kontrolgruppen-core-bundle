<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Form\ProcessTypeType;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/process_type")
 */
class ProcessTypeController extends BaseController
{
    /**
     * @Route("/", name="process_type_index", methods={"GET"})
     *
     * @param Request               $request
     * @param ProcessTypeRepository $processTypeRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ProcessTypeRepository $processTypeRepository): Response
    {
        return $this->render('@KontrolgruppenCore/process_type/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_types' => $processTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="process_type_new", methods={"GET","POST"})
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
        $processType = new ProcessType();
        $form = $this->createForm(ProcessTypeType::class, $processType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($processType);
            $entityManager->flush();

            return $this->redirectToRoute('process_type_index');
        }

        return $this->render('@KontrolgruppenCore/process_type/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_type' => $processType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_type_show", methods={"GET"})
     *
     * @param Request     $request
     * @param ProcessType $processType
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, ProcessType $processType): Response
    {
        return $this->render('@KontrolgruppenCore/process_type/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_type' => $processType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_type_edit", methods={"GET","POST"})
     *
     * @param Request     $request
     * @param ProcessType $processType
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, ProcessType $processType): Response
    {
        $form = $this->createForm(ProcessTypeType::class, $processType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('process_type_index', [
                'id' => $processType->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/process_type/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'process_type' => $processType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_type_delete", methods={"DELETE"})
     *
     * @param Request     $request
     * @param ProcessType $processType
     *
     * @return Response
     */
    public function delete(Request $request, ProcessType $processType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($processType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_type_index');
    }
}

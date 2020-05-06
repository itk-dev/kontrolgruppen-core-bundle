<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Form\QuickLinkType;
use Kontrolgruppen\CoreBundle\Repository\QuickLinkRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/quick_link")
 */
class QuickLinkController extends BaseController
{
    /**
     * @Route("/", name="quick_link_index", methods={"GET"})
     *
     * @param Request             $request
     * @param QuickLinkRepository $quickLinkRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, QuickLinkRepository $quickLinkRepository): Response
    {
        return $this->render('@KontrolgruppenCore/quick_link/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'quick_links' => $quickLinkRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quick_link_new", methods={"GET","POST"})
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
        $quickLink = new QuickLink();
        $form = $this->createForm(QuickLinkType::class, $quickLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quickLink);
            $entityManager->flush();

            return $this->redirectToRoute('quick_link_index');
        }

        return $this->render('@KontrolgruppenCore/quick_link/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'quick_link' => $quickLink,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quick_link_show", methods={"GET"})
     *
     * @param Request   $request
     * @param QuickLink $quickLink
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, QuickLink $quickLink): Response
    {
        return $this->render('@KontrolgruppenCore/quick_link/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'quick_link' => $quickLink,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quick_link_edit", methods={"GET","POST"})
     *
     * @param Request   $request
     * @param QuickLink $quickLink
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, QuickLink $quickLink): Response
    {
        $form = $this->createForm(QuickLinkType::class, $quickLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quick_link_index', [
                'id' => $quickLink->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/quick_link/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'quick_link' => $quickLink,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quick_link_delete", methods={"DELETE"})
     *
     * @param Request   $request
     * @param QuickLink $quickLink
     *
     * @return Response
     */
    public function delete(Request $request, QuickLink $quickLink): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quickLink->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quickLink);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quick_link_index');
    }
}

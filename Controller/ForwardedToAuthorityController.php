<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ForwardedToAuthority;
use Kontrolgruppen\CoreBundle\Form\ForwardedToAuthorityType;
use Kontrolgruppen\CoreBundle\Repository\ForwardedToAuthorityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/forwarded_to_authority")
 */
class ForwardedToAuthorityController extends BaseController
{
    /**
     * @Route("/", name="forwarded_to_authority_index", methods={"GET"})
     *
     * @param Request                        $request
     * @param ForwardedToAuthorityRepository $forwardedToAuthorityRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ForwardedToAuthorityRepository $forwardedToAuthorityRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('forwarded_to_authority/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'forwarded_to_authorities' => $forwardedToAuthorityRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="forwarded_to_authority_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $forwardedToAuthority = new ForwardedToAuthority();
        $form = $this->createForm(ForwardedToAuthorityType::class, $forwardedToAuthority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($forwardedToAuthority);
            $entityManager->flush();

            return $this->redirectToRoute('forwarded_to_authority_index');
        }

        return $this->render('forwarded_to_authority/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'forwarded_to_authority' => $forwardedToAuthority,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="forwarded_to_authority_show", methods={"GET"})
     *
     * @param Request              $request
     * @param ForwardedToAuthority $forwardedToAuthority
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, ForwardedToAuthority $forwardedToAuthority): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('forwarded_to_authority/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'forwarded_to_authority' => $forwardedToAuthority,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="forwarded_to_authority_edit", methods={"GET","POST"})
     *
     * @param Request              $request
     * @param ForwardedToAuthority $forwardedToAuthority
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, ForwardedToAuthority $forwardedToAuthority): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ForwardedToAuthorityType::class, $forwardedToAuthority);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('forwarded_to_authority_index');
        }

        return $this->render('forwarded_to_authority/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'forwarded_to_authority' => $forwardedToAuthority,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="forwarded_to_authority_delete", methods={"DELETE"})
     *
     * @param Request              $request
     * @param ForwardedToAuthority $forwardedToAuthority
     *
     * @return Response
     */
    public function delete(Request $request, ForwardedToAuthority $forwardedToAuthority): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$forwardedToAuthority->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($forwardedToAuthority);
            $entityManager->flush();
        }

        return $this->redirectToRoute('forwarded_to_authority_index');
    }
}

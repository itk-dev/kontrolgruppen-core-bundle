<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Reason;
use Kontrolgruppen\CoreBundle\Form\ReasonType;
use Kontrolgruppen\CoreBundle\Repository\ReasonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/reason")
 */
class ReasonController extends BaseController
{
    /**
     * @Route("/", name="reason_index", methods={"GET"})
     *
     * @param Request          $request
     * @param ReasonRepository $reasonRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ReasonRepository $reasonRepository): Response
    {
        return $this->render('@KontrolgruppenCore/reason/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'reasons' => $reasonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="reason_new", methods={"GET","POST"})
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
        $reason = new Reason();
        $form = $this->createForm(ReasonType::class, $reason);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reason);
            $entityManager->flush();

            return $this->redirectToRoute('reason_index');
        }

        return $this->render('@KontrolgruppenCore/reason/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'reason' => $reason,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reason_show", methods={"GET"})
     *
     * @param Request $request
     * @param Reason  $reason
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Reason $reason): Response
    {
        return $this->render('@KontrolgruppenCore/reason/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'reason' => $reason,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reason_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Reason  $reason
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Reason $reason): Response
    {
        $form = $this->createForm(ReasonType::class, $reason);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reason_index', [
                'id' => $reason->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/reason/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'reason' => $reason,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reason_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Reason  $reason
     *
     * @return Response
     */
    public function delete(Request $request, Reason $reason): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reason->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reason);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reason_index');
    }
}

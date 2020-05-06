<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Form\ServiceType;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/service")
 */
class ServiceController extends BaseController
{
    /**
     * @Route("/", name="service_index", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request               $request
     * @param \Kontrolgruppen\CoreBundle\Repository\ServiceRepository $serviceRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ServiceRepository $serviceRepository): Response
    {
        return $this->render('@KontrolgruppenCore/service/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="service_new", methods={"GET","POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function new(Request $request): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($service);
            $entityManager->flush();

            return $this->redirectToRoute('service_index');
        }

        return $this->render('@KontrolgruppenCore/service/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_show", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Service $service
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Service $service): Response
    {
        return $this->render('@KontrolgruppenCore/service/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'service' => $service,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_edit", methods={"GET","POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Service $service
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Service $service): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('service_index', [
                'id' => $service->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/service/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_delete", methods={"DELETE"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Service $service
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_index');
    }
}

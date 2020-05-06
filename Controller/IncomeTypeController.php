<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\IncomeType;
use Kontrolgruppen\CoreBundle\Form\IncomeTypeType;
use Kontrolgruppen\CoreBundle\Repository\IncomeTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/income_type")
 */
class IncomeTypeController extends BaseController
{
    /**
     * @Route("/", name="income_type_index", methods={"GET"})
     *
     * @param Request              $request
     * @param IncomeTypeRepository $incomeTypes
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, IncomeTypeRepository $incomeTypes): Response
    {
        return $this->render('@KontrolgruppenCore/income_type/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'incomeTypes' => $incomeTypes->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="income_type_new", methods={"GET","POST"})
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
        $incomeType = new IncomeType();
        $form = $this->createForm(IncomeTypeType::class, $incomeType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($incomeType);
            $entityManager->flush();

            return $this->redirectToRoute('income_type_index');
        }

        return $this->render('@KontrolgruppenCore/income_type/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'incomeType' => $incomeType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="income_type_show", methods={"GET"})
     *
     * @param Request    $request
     * @param IncomeType $incomeType
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, IncomeType $incomeType): Response
    {
        return $this->render('@KontrolgruppenCore/income_type/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'incomeType' => $incomeType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="income_type_edit", methods={"GET","POST"})
     *
     * @param Request    $request
     * @param IncomeType $incomeType
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, IncomeType $incomeType): Response
    {
        $form = $this->createForm(IncomeTypeType::class, $incomeType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('income_type_index', [
                'id' => $incomeType->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/income_type/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'incomeType' => $incomeType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="income_type_delete", methods={"DELETE"})
     *
     * @param Request    $request
     * @param IncomeType $incomeType
     *
     * @return Response
     */
    public function delete(Request $request, IncomeType $incomeType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$incomeType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($incomeType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('income_type_index');
    }
}

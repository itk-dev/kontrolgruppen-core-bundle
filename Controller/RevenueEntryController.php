<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\RevenueEntry;
use Kontrolgruppen\CoreBundle\Form\RevenueType;
use Kontrolgruppen\CoreBundle\Repository\RevenueEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/revenue/entry")
 */
class RevenueEntryController extends AbstractController
{
    /**
     * @Route("/", name="revenue_entry_index", methods={"GET"})
     */
    public function index(RevenueEntryRepository $revenueEntryRepository): Response
    {
        return $this->render('revenue_entry/index.html.twig', [
            'revenue_entries' => $revenueEntryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="revenue_entry_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $revenueEntry = new RevenueEntry();
        $form = $this->createForm(RevenueType::class, $revenueEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($revenueEntry);
            $entityManager->flush();

            return $this->redirectToRoute('revenue_entry_index');
        }

        return $this->render('revenue_entry/new.html.twig', [
            'revenue_entry' => $revenueEntry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="revenue_entry_show", methods={"GET"})
     */
    public function show(RevenueEntry $revenueEntry): Response
    {
        return $this->render('revenue_entry/show.html.twig', [
            'revenue_entry' => $revenueEntry,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="revenue_entry_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RevenueEntry $revenueEntry): Response
    {
        $form = $this->createForm(RevenueType::class, $revenueEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('revenue_entry_index');
        }

        return $this->render('revenue_entry/edit.html.twig', [
            'revenue_entry' => $revenueEntry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="revenue_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RevenueEntry $revenueEntry): Response
    {
        if ($this->isCsrfTokenValid('delete'.$revenueEntry->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($revenueEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('revenue_entry_index');
    }
}

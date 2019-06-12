<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Kontrolgruppen\CoreBundle\Form\EconomyEntryType;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/economy/entry")
 */
class EconomyEntryController extends AbstractController
{
    /**
     * @Route("/new", name="economy_entry_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $economyEntry = new EconomyEntry();
        $form = $this->createForm(EconomyEntryType::class, $economyEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($economyEntry);
            $entityManager->flush();

            return $this->redirectToRoute('economy_entry_index');
        }

        return $this->render('economy_entry/new.html.twig', [
            'economy_entry' => $economyEntry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="economy_entry_show", methods={"GET"})
     */
    public function show(EconomyEntry $economyEntry): Response
    {
        return $this->render('economy_entry/show.html.twig', [
            'economy_entry' => $economyEntry,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="economy_entry_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EconomyEntry $economyEntry): Response
    {
        $form = $this->createForm(EconomyEntryType::class, $economyEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('economy_entry_index', [
                'id' => $economyEntry->getId(),
            ]);
        }

        return $this->render('economy_entry/edit.html.twig', [
            'economy_entry' => $economyEntry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="economy_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EconomyEntry $economyEntry): Response
    {
        if ($this->isCsrfTokenValid('delete'.$economyEntry->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($economyEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('economy_entry_index');
    }
}

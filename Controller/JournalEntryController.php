<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Form\JournalEntryType;
use Kontrolgruppen\CoreBundle\Repository\JournalEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * @Route("/process/{process}/journal")
 */
class JournalEntryController extends BaseController
{
    /**
     * @Route("/", name="journal_entry_index", methods={"GET"})
     */
    public function index(JournalEntryRepository $journalEntryRepository, Process $process): Response
    {
        return $this->render('@KontrolgruppenCore/journal_entry/index.html.twig', [
            'journalEntries' => $journalEntryRepository->findAll(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/new", name="journal_entry_new", methods={"GET","POST"})
     */
    public function new(Request $request, Process $process): Response
    {
        $journalEntry = new JournalEntry();
        $journalEntry->setProcess($process);
        $form = $this->createForm(JournalEntryType::class, $journalEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journalEntry);
            $entityManager->flush();

            return $this->redirectToRoute('journal_entry_index');
        }

        return $this->render('@KontrolgruppenCore/journal_entry/new.html.twig', [
            'journalEntry' => $journalEntry,
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="journal_entry_show", methods={"GET"})
     */
    public function show(JournalEntry $journalEntry, Process $process): Response
    {
        return $this->render('@KontrolgruppenCore/journal_entry/show.html.twig', [
            'journalEntry' => $journalEntry,
            'process' => $process,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="journal_entry_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, JournalEntry $journalEntry, Process $process): Response
    {
        $form = $this->createForm(JournalEntryType::class, $journalEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('journal_entry_index', [
                'id' => $journalEntry->getId(),
                'process' => $process->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/journal_entry/edit.html.twig', [
            'journalEntry' => $journalEntry,
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="journal_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, JournalEntry $journalEntry, Process $process): Response
    {
        if ($this->isCsrfTokenValid('delete'.$journalEntry->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($journalEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('journal_entry_index', [
            'process' => $process->getId(),
        ]);
    }
}

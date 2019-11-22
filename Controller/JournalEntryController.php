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
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Filter\JournalFilterType;
use Kontrolgruppen\CoreBundle\Form\JournalEntryType;
use Kontrolgruppen\CoreBundle\Repository\JournalEntryRepository;
use Kontrolgruppen\CoreBundle\Service\LogManager;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/journal")
 */
class JournalEntryController extends BaseController
{
    /**
     * @Route("/latest", name="journal_entry_latest", methods={"GET"})
     */
    public function getLatestJournalEntries(
        Process $process,
        JournalEntryRepository $journalEntryRepository
    ) {
        return $this->render(
            '@KontrolgruppenCore/journal_entry/_journal_entry_latest_list.html.twig',
            [
                'journalEntries' => $journalEntryRepository->findBy(['process' => $process]),
            ]
        );
    }

    /**
     * @Route("/", name="journal_entry_index", methods={"GET","POST"})
     */
    public function index(
        Request $request,
        JournalEntryRepository $journalEntryRepository,
        Process $process,
        FilterBuilderUpdaterInterface $lexikBuilderUpdater,
        SessionInterface $session,
        LogManager $logManager,
        FormFactoryInterface $formFactory
    ): Response {
        $journalEntry = new JournalEntry();
        $journalEntry->setProcess($process);
        $journalEntryForm = $this->createForm(
            JournalEntryType::class,
            $journalEntry
        );
        $journalEntryForm->handleRequest($request);

        if ($journalEntryForm->isSubmitted() && $journalEntryForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journalEntry);
            $entityManager->flush();

            return $this->redirectToRoute(
                'journal_entry_index',
                ['process' => $process->getId()]
            );
        }

        $filterForm = $formFactory->create(JournalFilterType::class);

        $sortDirection = $request->query->get('sort_direction') ?: null;

        if (null !== $sortDirection) {
            $session->set('journal_entry_index_sort_direction', $sortDirection);
        } else {
            $sessionSortDirection = $session->get('journal_entry_index_sort_direction');

            $sortDirection = $sessionSortDirection ?: 'desc';
        }

        $onlyShowProcessStatusChanges = $request->query->get('only_show_status') ?: null;

        if (empty($onlyShowProcessStatusChanges)) {
            // initialize a query builder
            $qb = $journalEntryRepository->createQueryBuilder('e', 'e.id');

            if ($request->query->has($filterForm->getName())) {
                // manually bind values from the request
                $filterForm->submit($request->query->get($filterForm->getName()));

                // build the query from the given form object
                $lexikBuilderUpdater->addFilterConditions($filterForm, $qb);
            }

            $qb->andWhere('e.process = :process');
            $qb->setParameter('process', $process);

            $qb->orderBy('e.id', $sortDirection);

            $result = $qb->getQuery()->getArrayResult();

            // Attach log entries.
            // Only attach log entries if user is granted ROLE_ADMIN.
            if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
                $result = $logManager->attachLogEntriesToJournalEntries($result);
            }
        } else {
            $result = [];
        }

        $result = $logManager->attachProcessStatusChangesToJournalEntries(
            $result,
            $process,
            $sortDirection
        );

        return $this->render(
            '@KontrolgruppenCore/journal_entry/index.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'form' => $filterForm->createView(),
                'entries' => $result,
                'journalEntryForm' => $journalEntryForm->createView(),
                'process' => $process,
            ]
        );
    }

    /**
     * @Route("/new", name="journal_entry_new", methods={"GET","POST"})
     */
    public function new(Request $request, Process $process): Response
    {
        if (null !== $process->getCompletedAt() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('process_show', ['id' => $process->getId()]);
        }

        $journalEntry = new JournalEntry();
        $journalEntry->setProcess($process);
        $form = $this->createForm(JournalEntryType::class, $journalEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($journalEntry);
            $entityManager->flush();

            return $this->redirectToRoute(
                'journal_entry_index',
                ['process' => $process->getId()]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/journal_entry/new.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'journalEntry' => $journalEntry,
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="journal_entry_show", methods={"GET"})
     */
    public function show(
        Request $request,
        JournalEntry $journalEntry,
        Process $process,
        LogManager $logManager
    ): Response {
        // Attach log entries.
        // Only attach log entries if user is granted ROLE_ADMIN.
        if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $journalEntry = $logManager->attachLogEntriesToJournalEntry($journalEntry);
        }

        return $this->render(
            '@KontrolgruppenCore/journal_entry/show.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'journalEntry' => $journalEntry,
                'process' => $process,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="journal_entry_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        JournalEntry $journalEntry,
        Process $process,
        LogManager $logManager
    ): Response {
        $form = $this->createForm(JournalEntryType::class, $journalEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('journal_entry_index', [
                'id' => $journalEntry->getId(),
                'process' => $process->getId(),
            ]);
        }

        // Attach log entries.
        // Only attach log entries if user is granted ROLE_ADMIN.
        if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $journalEntry = $logManager->attachLogEntriesToJournalEntry($journalEntry);
        }

        return $this->render(
            '@KontrolgruppenCore/journal_entry/edit.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'journalEntry' => $journalEntry,
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="journal_entry_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        JournalEntry $journalEntry,
        Process $process
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete'.$journalEntry->getId(),
            $request->request->get('_token')
        )) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($journalEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('journal_entry_index', [
            'process' => $process->getId(),
        ]);
    }
}

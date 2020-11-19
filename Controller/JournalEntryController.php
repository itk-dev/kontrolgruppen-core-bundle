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
     *
     * @param Process                $process
     * @param JournalEntryRepository $journalEntryRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatestJournalEntries(Process $process, JournalEntryRepository $journalEntryRepository)
    {
        return $this->render(
            '@KontrolgruppenCore/journal_entry/_journal_entry_latest_list.html.twig',
            [
                'journalEntries' => $journalEntryRepository->findBy(['process' => $process]),
            ]
        );
    }

    /**
     * @Route("/", name="journal_entry_index", methods={"GET","POST"})
     *
     * @param Request                       $request
     * @param JournalEntryRepository        $journalEntryRepository
     * @param Process                       $process
     * @param FilterBuilderUpdaterInterface $lexikBuilderUpdater
     * @param SessionInterface              $session
     * @param LogManager                    $logManager
     * @param FormFactoryInterface          $formFactory
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, JournalEntryRepository $journalEntryRepository, Process $process, FilterBuilderUpdaterInterface $lexikBuilderUpdater, SessionInterface $session, LogManager $logManager, FormFactoryInterface $formFactory): Response
    {
        $journalEntryFormView = null;

        if (null === $process->getCompletedAt()) {
            if ($this->isGranted('edit', $process)) {
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

                $journalEntryFormView = $journalEntryForm->createView();
            }
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

            $qb->orderBy('e.createdAt', $sortDirection);

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

        $result = $logManager->attachProcessGroupChangesToJournalEntries(
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
                'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
                'form' => $filterForm->createView(),
                'entries' => $result,
                'journalEntryForm' => $journalEntryFormView,
                'process' => $process,
            ]
        );
    }

    /**
     * @Route("/new", name="journal_entry_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function new(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('journal_entry_index', ['process' => $process->getId()]);
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
     *
     * @param Request      $request
     * @param JournalEntry $journalEntry
     * @param Process      $process
     * @param LogManager   $logManager
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, JournalEntry $journalEntry, Process $process, LogManager $logManager): Response
    {
        // If opening a journal entry that does not belong to the process, redirect to index.
        if ($journalEntry->getProcess() !== $process) {
            return $this->redirectToRoute(
                'journal_entry_index',
                ['process' => $process->getId()]
            );
        }

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
                'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
                'journalEntry' => $journalEntry,
                'process' => $process,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="journal_entry_edit", methods={"GET","POST"})
     *
     * @param Request      $request
     * @param JournalEntry $journalEntry
     * @param Process      $process
     * @param LogManager   $logManager
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, JournalEntry $journalEntry, Process $process, LogManager $logManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // If opening a journal entry that does not belong to the process, redirect to index.
        if ($journalEntry->getProcess() !== $process) {
            return $this->redirectToRoute(
                'journal_entry_index',
                ['process' => $process->getId()]
            );
        }

        // Redirect to show if process is completed.
        $this->redirectOnProcessComplete($process, 'journal_entry_show', [
            'id' => $journalEntry->getId(),
            'process' => $process->getId(),
        ]);

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
                'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
                'journalEntry' => $journalEntry,
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="journal_entry_delete", methods={"DELETE"})
     *
     * @param Request      $request
     * @param JournalEntry $journalEntry
     * @param Process      $process
     *
     * @return Response
     */
    public function delete(Request $request, JournalEntry $journalEntry, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // If trying to delete a journal entry that does not belong to the process, redirect to index.
        if ($journalEntry->getProcess() !== $process ||
            null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('journal_entry_index', ['process' => $process->getId()]);
        }

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

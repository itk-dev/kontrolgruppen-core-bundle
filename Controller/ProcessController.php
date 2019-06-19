<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Event\Doctrine\ORM\OnReadEventArgs;
use Kontrolgruppen\CoreBundle\Filter\ProcessFilterType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Kontrolgruppen\CoreBundle\Entity\Client;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;

/**
 * @Route("/process")
 */
class ProcessController extends BaseController
{
    /**
     * @Route("/", name="process_index", methods={"GET"})
     */
    public function index(
        Request $request,
        ProcessRepository $processRepository,
        FilterBuilderUpdaterInterface $lexikBuilderUpdater,
        PaginatorInterface $paginator
    ): Response {
        $filterForm = $this->get('form.factory')->create(ProcessFilterType::class);

        $results = [];

        $qb = null;

        $selectedCaseWorker = $filterForm->get('caseWorker');

        if (null === $selectedCaseWorker->getData()) {
            $filterForm->get('caseWorker')->setData($this->getUser()->getId());
        }

        if ($request->query->has($filterForm->getName())) {
            $formParameters = $request->query->get($filterForm->getName());

            if (!isset($formParameters['caseWorker'])) {
                $formParameters['caseWorker'] = $this->getUser()->getId();
            }

            // manually bind values from the request
            $filterForm->submit($formParameters);

            // initialize a query builder
            $qb = $processRepository->createQueryBuilder('e');

            // build the query from the given form object
            $lexikBuilderUpdater->addFilterConditions($filterForm, $qb);
        } else {
            $qb = $processRepository->createQueryBuilder('e');

            $qb->where('e.caseWorker = :caseWorker');
            $qb->setParameter(':caseWorker', $this->getUser());
        }

        // Add sortable fields.
        $qb->leftJoin('e.caseWorker', 'caseWorker');
        $qb->addSelect('partial caseWorker.{id}');

        $qb->leftJoin('e.channel', 'channel');
        $qb->addSelect('partial channel.{id,name}');

        $qb->leftJoin('e.service', 'service');
        $qb->addSelect('partial service.{id,name}');

        $qb->leftJoin('e.processType', 'processType');
        $qb->addSelect('partial processType.{id}');

        $qb->leftJoin('e.processStatus', 'processStatus');
        $qb->addSelect('partial processStatus.{id}');

        $query = $qb->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            50
        );

        return $this->render(
            '@KontrolgruppenCore/process/index.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo()
                ),
                'processes' => $results,
                'pagination' => $pagination,
                'form' => $filterForm->createView(),
                'query' => $query,
            ]
        );
    }

    /**
     * @Route("/new", name="process_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProcessManager $processManager): Response
    {
        $process = new Process();
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process = $processManager->newProcess($process);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($process);

            $client = new Client();
            $process->setClient($client);

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('process_index');
        }

        return $this->render(
            '@KontrolgruppenCore/process/new.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Process $process): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $eventManager = $this->getDoctrine()->getManager()->getEventManager();
        $eventManager->dispatchEvent('onRead', new OnReadEventArgs($entityManager, $process));

        // Latest journal entries.
        $latestJournalEntries = $this->getDoctrine()->getRepository(
            JournalEntry::class
        )->getLatestEntries($process);

        // Latest Log entries
        $latestLogEntries = $this->getDoctrine()->getRepository(
            ProcessLogEntry::class
        )->getLatestEntries($process);

        return $this->render(
            '@KontrolgruppenCore/process/show.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'latestJournalEntries' => $latestJournalEntries,
                'latestLogEntries' => $latestLogEntries,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="process_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process): Response
    {
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'process_index',
                [
                    'id' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/process/edit.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="process_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Process $process): Response
    {
        if ($this->isCsrfTokenValid(
            'delete'.$process->getId(),
            $request->request->get('_token')
        )) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($process);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_index');
    }
}

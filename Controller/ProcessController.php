<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\Client;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\LockedNetValue;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Filter\ProcessFilterType;
use Kontrolgruppen\CoreBundle\Form\ProcessCompleteType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Service\LockService;
use Kontrolgruppen\CoreBundle\Service\LogManager;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Kontrolgruppen\CoreBundle\Service\UserSettingsService;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        PaginatorInterface $paginator,
        FormFactoryInterface $formFactory,
        ProcessManager $processManager,
        UserRepository $userRepository,
        UserSettingsService $userSettingsService
    ): Response {
        $userSettings = $this->getUser()->getUserSettings();

        $result = $userSettingsService->handleProcessIndexRequest($request, $userSettings);

        $filterForm = $formFactory->create(ProcessFilterType::class);

        if (!empty($result)) {
            return $this->redirectToRoute(
                'process_index',
                [
                    $filterForm->getName() => $request->query->get($filterForm->getName()),
                    'sort' => $result['sort'],
                    'direction' => $result['direction'],
                    'page' => $request->query->get('page'),
                ]
            );
        }

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

        $qb->leftJoin('e.reason', 'reason');
        $qb->addSelect('partial reason.{id,name}');

        $qb->leftJoin('e.service', 'service');
        $qb->addSelect('partial service.{id,name}');

        $qb->leftJoin('e.processType', 'processType');
        $qb->addSelect('partial processType.{id}');

        $qb->leftJoin('e.processStatus', 'processStatus');
        $qb->addSelect('partial processStatus.{id}');

        $query = $qb->getQuery();

        $caseWorker = (!empty($selectedCaseWorker->getData()))
            ? $userRepository->find($selectedCaseWorker->getData())
            : $this->getUser();

        $notVisitedProcesses = $processManager->getUsersUnvisitedProcesses($caseWorker);
        $processes = $processManager->markProcessesAsUnvisited(
            $notVisitedProcesses,
            $query->getResult()
        );

        $pagination = $paginator->paginate(
            $processes,
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
    public function new(
        Request $request,
        ProcessManager $processManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CprServiceInterface $cprService,
        LockService $lockService
    ): Response {
        $process = new Process();

        $this->denyAccessUnlessGranted('edit', $process);

        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process = $processManager->newProcess($process);

            $lockService->createLock($process->getCaseNumber());

            if (!$lockService->isAcquired($process->getCaseNumber())) {
                throw new \RuntimeException('Lock was not acquired.');
            }

            $process = $this->storeProcess($process, $translator, $logger, $cprService);

            $lockService->release($process->getCaseNumber());

            return $this->redirectToRoute('client_show', ['process' => $process]);
        }

        // Get latest log entries
        $recentActivity = $this->getDoctrine()->getRepository(
            ProcessLogEntry::class
        )->getLatestEntriesByLevel(ProcessLogEntryLevelEnumType::NOTICE, 10);

        return $this->render(
            '@KontrolgruppenCore/process/new.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
                'recentActivity' => $recentActivity,
            ]
        );
    }

    private function storeProcess(Process $process, TranslatorInterface $translator, LoggerInterface $logger, CprServiceInterface $cprService): Process
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($process);

        $client = new Client();

        try {
            $client = $cprService->populateClient(new Cpr($process->getClientCPR()), $client);
        } catch (CprException $e) {
            $logger->error($e);
        }

        $process->setClient($client);
        $entityManager->persist($client);

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            $logger->log(LogLevel::ERROR, $e);

            $this->addFlash(
                'danger',
                $translator->trans('process.new.unique_error')
            );
        }

        return $process;
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Process $process, LogManager $logManager): Response
    {
        // Latest journal entries.
        $latestJournalEntries = $this->getDoctrine()->getRepository(
            JournalEntry::class
        )->getLatestEntries($process);

        if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $latestJournalEntries = $logManager->attachLogEntriesToJournalEntries($latestJournalEntries);
        }

        return $this->render(
            '@KontrolgruppenCore/process/show.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'latestJournalEntries' => $latestJournalEntries,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="process_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt() && !$this->isGranted('ROLE_ADMIN')) {
            $this->redirectToRoute('process_show', ['id' => $process->getId()]);
        }

        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToReferer(
                'process_show',
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
        $this->denyAccessUnlessGranted('edit', $process);

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

    /**
     * @Route("/{id}/complete", name="process_complete", methods={"GET","POST"})
     */
    public function complete(Request $request, Process $process, ServiceRepository $serviceRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute(
                'process_show',
                ['id' => $process->getId()]
            );
        }

        $form = $this->createForm(ProcessCompleteType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $services = $serviceRepository->getByProcess($process);
            foreach ($services as $service) {
                $lockedNetValue = new LockedNetValue();
                $lockedNetValue->setService($service);
                $lockedNetValue->setProcess($process);
                $lockedNetValue->setValue($service->getNetDefaultValue());

                $em->persist($lockedNetValue);
            }

            $process->setCompletedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($process);
            $em->flush();

            return $this->redirectToRoute(
                'process_show',
                [
                    'id' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/process/complete.html.twig',
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
     * @Route("/{id}/resume", name="process_resume", methods={"POST"})
     */
    public function resume(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $process->setCompletedAt(null);
        $process->setLockedNetValue(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($process);
        $em->flush();

        return $this->redirectToRoute('process_show', ['id' => $process->getId()]);
    }
}

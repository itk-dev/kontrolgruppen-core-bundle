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
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\LockedNetValue;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Filter\ProcessFilterType;
use Kontrolgruppen\CoreBundle\Form\ProcessCompleteType;
use Kontrolgruppen\CoreBundle\Form\ProcessResumeType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Kontrolgruppen\CoreBundle\Service\LogManager;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Kontrolgruppen\CoreBundle\Service\UserSettingsService;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process")
 */
class ProcessController extends BaseController
{
    /**
     * @Route("/", name="process_index", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request                           $request
     * @param \Kontrolgruppen\CoreBundle\Repository\ProcessRepository             $processRepository
     * @param \Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface $lexikBuilderUpdater
     * @param \Knp\Component\Pager\PaginatorInterface                             $paginator
     * @param \Symfony\Component\Form\FormFactoryInterface                        $formFactory
     * @param \Kontrolgruppen\CoreBundle\Service\ProcessManager                   $processManager
     * @param \Kontrolgruppen\CoreBundle\Repository\UserRepository                $userRepository
     * @param \Kontrolgruppen\CoreBundle\Service\UserSettingsService              $userSettingsService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ProcessRepository $processRepository, FilterBuilderUpdaterInterface $lexikBuilderUpdater, PaginatorInterface $paginator, FormFactoryInterface $formFactory, ProcessManager $processManager, UserRepository $userRepository, UserSettingsService $userSettingsService): Response
    {
        $filterForm = $formFactory->create(ProcessFilterType::class);

        $queryBuilder = null;

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
            $queryBuilder = $processRepository->createQueryBuilder('e');

            // build the query from the given form object
            $lexikBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
        } else {
            $queryBuilder = $processRepository->createQueryBuilder('e');

            $queryBuilder->where('e.caseWorker = :caseWorker');
            $queryBuilder->setParameter(':caseWorker', $this->getUser());
        }

        // Add sortable fields.
        $queryBuilder->leftJoin('e.caseWorker', 'caseWorker');
        $queryBuilder->addSelect('partial caseWorker.{id}');

        $queryBuilder->leftJoin('e.channel', 'channel');
        $queryBuilder->addSelect('partial channel.{id,name}');

        $queryBuilder->leftJoin('e.reason', 'reason');
        $queryBuilder->addSelect('partial reason.{id,name}');

        $queryBuilder->leftJoin('e.service', 'service');
        $queryBuilder->addSelect('partial service.{id,name}');

        $queryBuilder->leftJoin('e.processType', 'processType');
        $queryBuilder->addSelect('partial processType.{id}');

        $queryBuilder->leftJoin('e.processStatus', 'processStatus');
        $queryBuilder->addSelect('partial processStatus.{id}');

        $formKey = 'process_index.'.$filterForm->getName();
        /* @var \Kontrolgruppen\CoreBundle\Entity\User $user */
        $user = $this->getUser();

        $paginatorOptions = [
            'defaultSortFieldName' => 'e.caseNumber',
            'defaultSortDirection' => 'desc',
        ];

        // Get sort and direction from user settings.
        if (!$request->query->has('sort') && !$request->query->has('direction')) {
            $userSettings = $userSettingsService->getSettings($user, $formKey);

            if ($userSettings && null !== $userSettings->getSettingsValue()) {
                $userSettingsValue = $userSettings->getSettingsValue();
                $paginatorOptions = [
                    'defaultSortFieldName' => $userSettingsValue['sort'],
                    'defaultSortDirection' => $userSettingsValue['direction'],
                ];
            }
        } else {
            $userSettingsService->setSettings($user, $formKey, [
                'sort' => $request->query->get('sort'),
                'direction' => $request->query->get('direction'),
            ]);
        }

        $query = $queryBuilder->getQuery();

        // Get paginated result.
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            50,
            $paginatorOptions
        );

        // Find Processes that have not been visited by the assigned CaseWorker.
        $caseWorker = (!empty($selectedCaseWorker->getData()))
            ? $userRepository->find($selectedCaseWorker->getData())
            : $this->getUser();
        $foundEntries = array_column($query->getArrayResult(), 'id');
        $notVisitedProcessIds = $processManager->getUsersUnvisitedProcessIds($foundEntries, $caseWorker);

        return $this->render(
            '@KontrolgruppenCore/process/index.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo()
                ),
                'unvisitedProcessIds' => $notVisitedProcessIds,
                'pagination' => $pagination,
                'form' => $filterForm->createView(),
                'query' => $query,
            ]
        );
    }

    /**
     * @Route("/new", name="process_new", methods={"GET","POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request         $request
     * @param \Kontrolgruppen\CoreBundle\Service\ProcessManager $processManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function new(Request $request, ProcessManager $processManager): Response
    {
        $process = new Process();

        $this->denyAccessUnlessGranted('edit', $process);

        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process = $processManager->newProcess($process);

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

    /**
     * @Route("/search-process-by-cpr", name="process_search_by_cpr", methods={"POST"})
     *
     * @param Request           $request
     * @param ProcessRepository $processRepository
     *
     * @return Response
     */
    public function searchProcessesByCpr(Request $request, ProcessRepository $processRepository): Response
    {
        if (!$request->request->has('cpr')) {
            throw new NotFoundHttpException('No CPR found!');
        }

        $processes = $processRepository->findBy(
            ['clientCPR' => $request->request->get('cpr')]
        );

        return $this->render(
            '@KontrolgruppenCore/process/_process_search_cpr_result.html.twig',
            ['processes' => $processes]
        );
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET", "POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request     $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process     $process
     * @param \Kontrolgruppen\CoreBundle\Service\LogManager $logManager
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('delete', $process);

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
     *
     * @param \Symfony\Component\HttpFoundation\Request                     $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process                     $process
     * @param \Kontrolgruppen\CoreBundle\Repository\ServiceRepository       $serviceRepository
     * @param \Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository $processStatusRepository
     * @param \Kontrolgruppen\CoreBundle\Service\EconomyService             $economyService
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function complete(Request $request, Process $process, ServiceRepository $serviceRepository, ProcessStatusRepository $processStatusRepository, ProcessManager $processManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute(
                'process_show',
                ['id' => $process->getId()]
            );
        }

        $availableStatuses = $processStatusRepository->getAvailableCompletingStatusForProcessType($process->getProcessType());

        $form = $this->createForm(ProcessCompleteType::class, $process, [
            'available_statuses' => $availableStatuses,
        ]);
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

            $em->flush();

            $processManager->completeProcess($process);

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
     * @Route("/{id}/resume", name="process_resume", methods={"POST", "GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resume(Request $request, Process $process, ProcessStatusRepository $processStatusRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $form = $this->createForm(ProcessResumeType::class, $process, [
            'available_statuses' => $processStatusRepository->getAvailableForProcess($process),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process->setCompletedAt(null);
            $process->setLockedNetValue(null);
            $process->setLastReopened(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($process);
            $em->flush();

            return $this->redirectToRoute('process_show', ['id' => $process->getId()]);
        }

        return $this->render(
            '@KontrolgruppenCore/process/resume.html.twig',
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
}

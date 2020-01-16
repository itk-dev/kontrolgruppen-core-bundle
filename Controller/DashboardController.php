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
use Kontrolgruppen\CoreBundle\DBAL\Types\DateIntervalType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 */
class DashboardController extends BaseController
{
    /**
     * @Route("", name="dashboard_index")
     */
    public function index(Request $request, ReminderRepository $reminderRepository, ProcessRepository $processRepository, PaginatorInterface $paginator, SessionInterface $session, ProcessManager $processManager)
    {
        if ($this->isGranted('ROLE_EXTERNAL')) {
            return $this->redirectToRoute('search_external');
        }

        $filterFormBuilder = $this->createFormBuilder(null, [
            'attr' => [
                'id' => 'dashboard_process_limit',
            ],
        ]);
        $filterFormBuilder->add('limit', ChoiceType::class, [
            'choices' => [
                '2' => 2,
                '5' => 5,
                '10' => 10,
                '50' => 50,
            ],
            'choice_translation_domain' => false,
            'label_attr' => ['class' => 'sr-only'],
            'label' => 'dashboard.my_processes.limit',
        ]);
        $filterForm = $filterFormBuilder->getForm();

        $qb = $processRepository->createQueryBuilder('e');

        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filterFormData = $filterForm->getData();
            $limit = $filterFormData['limit'];

            $session->set('dashboard_my_processes_limit', $limit);
        } else {
            $limit = $session->get('dashboard_my_processes_limit') ?: 10;

            $filterForm->get('limit')->setData($limit);
        }

        // Only find current user's processes.
        $qb->where('e.caseWorker = :caseWorker');
        $qb->setParameter(':caseWorker', $this->getUser());
        $qb->orderBy('e.id', 'DESC');

        // Only find processes where the processType.hideInDashboard is not true.
        $qb->leftJoin('e.processType', 'processType');
        $qb->addSelect('partial processType.{id,name,hideInDashboard}');
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('processType.hideInDashboard'),
                $qb->expr()->eq('processType.hideInDashboard', 'false')
            )
        );

        $qb->andWhere('e.completedAt is null');

        $query = $qb->getQuery();

        $notVisitedProcesses = $processManager->getUsersUnvisitedProcesses($this->getUser());

        $myProcesses = $processManager->markProcessesAsUnvisited(
            $notVisitedProcesses,
            $query->getResult()
        );

        $pagination = $paginator->paginate(
            $myProcesses,
            $request->query->get('page', 1),
            $limit
        );

        // Coming reminders form.
        $comingReminderForm = $this->createFormBuilder()->add('date_interval', ChoiceType::class, [
            'choices' => DateIntervalType::getChoices(),
            'label' => 'dashboard.coming_reminders.label',
        ])->getForm();

        return $this->render('@KontrolgruppenCore/dashboard/index.html.twig', [
            'reminders' => $reminderRepository->findActiveUserReminders($this->getUser()),
            'unassignedProcesses' => $processRepository->findBy(['caseWorker' => null]),
            'myProcesses' => $pagination,
            'comingReminderForm' => $comingReminderForm->createView(),
            'myProcessesFilterForm' => $filterForm->createView(),
            'notVisitedProcesses' => $notVisitedProcesses,
        ]);
    }
}

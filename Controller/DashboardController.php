<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\DBAL\Types\DateIntervalType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Kontrolgruppen\CoreBundle\Filter\DashboardProcessFilterType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class DashboardController.
 */
class DashboardController extends BaseController
{
    /**
     * @Route("", name="dashboard_index")
     */
    public function index(Request $request, ReminderRepository $reminderRepository, ProcessRepository $processRepository, PaginatorInterface $paginator, SessionInterface $session)
    {
        $filterFormBuilder = $this->createFormBuilder();
        $filterFormBuilder->add('limit', ChoiceType::class, [
            'choices' => [
                '2' => 2,
                '5' => 5,
                '10' => 10,
                '50' => 50,
            ],
            'label_attr' => array('class' => 'sr-only'),
        ]);
        $filterForm = $filterFormBuilder->getForm();

        $qb = $processRepository->createQueryBuilder('e');

        $filterForm->handleRequest($request);

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filterFormData = $filterForm->getData();
            $limit = $filterFormData['limit'];

            $session->set('dashboard_my_processes_limit', $limit);
        }
        else {
            $limit = $session->get('dashboard_my_processes_limit') ?: 10;

            $filterForm->get('limit')->setData($limit);
        }

        $qb->where('e.caseWorker = :caseWorker');
        $qb->setParameter(':caseWorker', $this->getUser());
        $qb->orderBy('e.id', 'DESC');

        $query = $qb->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $limit
        );

        $qb = $processRepository->createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->where('e.caseWorker = :caseWorker');
        $qb->setParameter(':caseWorker', $this->getUser());
        $myProcessesLength = $qb->getQuery()->getSingleScalarResult();

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
            'myProcessesLength' => $myProcessesLength,
        ]);
    }
}

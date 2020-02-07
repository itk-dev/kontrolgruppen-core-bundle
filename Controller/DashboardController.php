<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\DBAL\Types\DateIntervalType;
use Kontrolgruppen\CoreBundle\Entity\User;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Kontrolgruppen\CoreBundle\Service\UserSettingsService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 */
class DashboardController extends BaseController
{
    /**
     * @Route("", name="dashboard_index")
     *
     * @param Request             $request
     * @param ReminderRepository  $reminderRepository
     * @param ProcessRepository   $processRepository
     * @param PaginatorInterface  $paginator
     * @param ProcessManager      $processManager
     * @param UserSettingsService $userSettingsService
     *   The user settings service
     *
     * @return RedirectResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function index(Request $request, ReminderRepository $reminderRepository, ProcessRepository $processRepository, PaginatorInterface $paginator, ProcessManager $processManager, UserSettingsService $userSettingsService)
    {
        // Redirect if global menu contains only a single item.
        $menu = $this->getGlobalNavMenu();
        if (1 === \count($menu)) {
            $menuItem = reset($menu);

            return $this->redirect($menuItem->path);
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

        $queryBuilder = $processRepository->createQueryBuilder('e');

        $filterForm->handleRequest($request);

        $formKey = 'dashboard_index.'.$filterForm->getName();
        /* @var User $user */
        $user = $this->getUser();

        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $filterFormData = $filterForm->getData();
            $limit = $filterFormData['limit'];

            $userSettingsService->setSettings($user, $formKey, [
                'limit' => $limit,
            ]);
        } else {
            $userSettings = $userSettingsService->getSettings($user, $formKey);
            $userSettingsValue = null !== $userSettings ? $userSettings->getSettingsValue() : null;

            if ($userSettingsValue !== null) {
                $limit = $userSettings->getSettingsValue() ?? null;

                if ($limit !== null) {
                    $filterForm->get('limit')->setData($limit);
                }
            }
        }

        // Only find current user's processes.
        $queryBuilder->where('e.caseWorker = :caseWorker');
        $queryBuilder->setParameter(':caseWorker', $this->getUser());
        $queryBuilder->orderBy('e.id', 'DESC');

        // Only find processes where the processType.hideInDashboard is not true.
        $queryBuilder->leftJoin('e.processType', 'processType');
        $queryBuilder->addSelect('partial processType.{id,name,hideInDashboard}');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->isNull('processType.hideInDashboard'),
                $queryBuilder->expr()->eq('processType.hideInDashboard', 'false')
            )
        );

        $queryBuilder->andWhere('e.completedAt is null');

        $query = $queryBuilder->getQuery();

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

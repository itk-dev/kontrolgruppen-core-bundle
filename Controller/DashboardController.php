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
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 */
class DashboardController extends BaseController
{
    /**
     * @Route("", name="main")
     */
    public function index(ReminderRepository $reminderRepository, ProcessRepository $processRepository)
    {
        $comingReminderForm = $this->createFormBuilder()->add('date_interval', ChoiceType::class, [
            'choices' => DateIntervalType::getChoices()
        ])->getForm();

        return $this->render('@KontrolgruppenCore/dashboard/index.html.twig', [
            'reminders' => $reminderRepository->findActiveUserReminders($this->getUser()),
            'unassignedProcesses' => $processRepository->findBy(['caseWorker' => null]),
            'myProcesses' => $processRepository->findBy(['caseWorker' => $this->getUser()]),
            'comingReminderForm' => $comingReminderForm->createView(),
        ]);
    }
}

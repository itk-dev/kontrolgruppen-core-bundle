<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
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
        return $this->render('@KontrolgruppenCore/dashboard/index.html.twig', [
            'reminders' => $reminderRepository->findActiveUserReminders($this->getUser()),
            'unassignedProcesses' => $processRepository->findBy(['caseWorker' => null]),
            'myProcesses' => $processRepository->findBy(['caseWorker' => $this->getUser()]),
        ]);
    }
}

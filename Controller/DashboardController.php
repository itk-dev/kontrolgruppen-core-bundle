<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends BaseController
{
    /**
     * @Route("", name="main")
     */
    public function index(ReminderRepository $reminderRepository)
    {
        return $this->render('@KontrolgruppenCore/dashboard/index.html.twig', [
            'reminders' => $reminderRepository->findActiveUserReminders($this->getUser()),
        ]);
    }
}

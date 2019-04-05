<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Form\ReminderType;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reminder")
 */
class ReminderController extends BaseController
{
    /**
     * @Route("/", name="user_reminder_index", methods={"GET"})
     */
    public function index(ReminderRepository $reminderRepository): Response
    {
        return $this->render('@KontrolgruppenCore/reminder/user_reminder_index.html.twig', [
            'reminders' => $reminderRepository->findActiveUserReminders($this->getUser()),
        ]);
    }

    /**
     * @Route("/all", name="user_reminder_all", methods={"GET"})
     */
    public function all(ReminderRepository $reminderRepository): Response
    {
        return $this->render('@KontrolgruppenCore/reminder/user_reminder_index.html.twig', [
            'reminders' => $reminderRepository->findAllUserReminders($this->getUser()),
        ]);
    }
}

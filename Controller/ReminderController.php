<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Kontrolgruppen\CoreBundle\Repository\ReminderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reminder")
 */
class ReminderController extends BaseController
{
    /**
     * @Route("/", name="user_reminder_index", methods={"GET"})
     *
     * @param ReminderRepository $reminderRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(ReminderRepository $reminderRepository): Response
    {
        return $this->render('@KontrolgruppenCore/reminder/user_reminder_index.html.twig', [
            'reminders' => $reminderRepository->findActiveUserReminders($this->getUser()),
        ]);
    }

    /**
     * @Route("/all", name="user_reminder_all", methods={"GET"})
     *
     * @param ReminderRepository $reminderRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function all(ReminderRepository $reminderRepository): Response
    {
        return $this->render('@KontrolgruppenCore/reminder/user_reminder_index.html.twig', [
            'reminders' => $reminderRepository->findAllUserReminders($this->getUser()),
        ]);
    }

    /**
     * @Route("/latest/{interval}", name="user_reminder_get_latest", methods={"GET"})
     *
     * @param string             $interval
     * @param ReminderRepository $reminderRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatestReminders(string $interval, ReminderRepository $reminderRepository)
    {
        return $this->render('@KontrolgruppenCore/reminder/_reminder_latest_list.html.twig', [
            'reminders' => $reminderRepository->findComingUserReminders($this->getUser(), $interval, true),
        ]);
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Kontrolgruppen\CoreBundle\Form\ReminderType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/reminder")
 */
class ProcessReminderController extends BaseController
{
    /**
     * @Route("/", name="reminder_index", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        return $this->render('@KontrolgruppenCore/reminder/index.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'reminders' => $process->getReminders(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/new", name="reminder_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function new(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $reminder = new Reminder();
        $reminder->setProcess($process);
        $reminder->setDate(
            (new \DateTime())->setTime(8, 0)->modify('+1 day')
        );
        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reminder);
            $entityManager->flush();

            return $this->redirectToRoute('reminder_index', ['process' => $process->getId()]);
        }

        return $this->render('@KontrolgruppenCore/reminder/new.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'reminder' => $reminder,
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reminder_show", methods={"GET"})
     *
     * @param Request  $request
     * @param Reminder $reminder
     * @param Process  $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Reminder $reminder, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        return $this->render('@KontrolgruppenCore/reminder/show.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'reminder' => $reminder,
            'process' => $process,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reminder_edit", methods={"GET","POST"})
     *
     * @param Request  $request
     * @param Reminder $reminder
     * @param Process  $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Reminder $reminder, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $form = $this->createForm(ReminderType::class, $reminder);

        // Add finished for edit form only.
        $form->add('finished', null, [
            'label' => 'reminder.form.finished',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reminder_index', [
                'id' => $reminder->getId(),
                'process' => $process->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/reminder/edit.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'reminder' => $reminder,
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reminder_delete", methods={"DELETE"})
     *
     * @param Request  $request
     * @param Reminder $reminder
     * @param Process  $process
     *
     * @return Response
     */
    public function delete(Request $request, Reminder $reminder, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if ($this->isCsrfTokenValid('delete'.$reminder->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reminder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reminder_index', [
            'process' => $process->getId(),
        ]);
    }

    /**
     * @Route("{id}/finish", name="reminder_finish", methods={"GET", "POST"})
     *
     * @param Reminder $reminder
     * @param Process  $process
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function finishReminder(Reminder $reminder, Process $process)
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $reminder->setFinished(true);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('reminder_index', [
            'process' => $process->getId(),
        ]);
    }
}

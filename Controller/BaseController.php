<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController.
 */
class BaseController extends AbstractController
{
    protected $requestStack;
    protected $menuService;

    /**
     * BaseController constructor.
     *
     * @param RequestStack $requestStack
     * @param MenuService  $menuService
     */
    public function __construct(RequestStack $requestStack, MenuService $menuService)
    {
        $this->requestStack = $requestStack;
        $this->menuService = $menuService;
    }

    /**
     * Render view.
     * Attaches menu and quick links.
     *
     * @param string        $view
     * @param array         $parameters
     * @param Response|null $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function render(string $view, array $parameters = [], Response $response = null): Response
    {
        // Set reminders
        $numberOfReminders = $this->getDoctrine()->getRepository(
            Reminder::class
        )->findNumberOfActiveUserReminders($this->getUser());
        $parameters['activeUserReminders'] = $numberOfReminders;

        // Set quickLinks
        $quickLinks = $this->getDoctrine()
            ->getRepository(QuickLink::class)
            ->findAll();
        $parameters['quickLinks'] = $quickLinks;

        // Get current path.
        $request = $this->requestStack->getCurrentRequest();
        $path = $request->getPathInfo();
        $parameters['path'] = $path;

        // Add global navigation.
        $parameters['globalMenuItems'] = $this->getGlobalNavMenu($path);

        if ('process_complete' !== $request->get('_route')) {
            // If this is a route beneath the proces/{id}/, attach changeProcessStatusForm.
            if (1 === preg_match('/^\/process\/[0-9]+/', $path) && isset($parameters['process'])) {
                $changeProcessStatusForm = $this->createChangeProcessStatusForm($parameters['process']);
                $this->handleChangeProcessStatusForm(
                    $request,
                    $changeProcessStatusForm
                );

                $parameters['changeProcessStatusForm'] = $changeProcessStatusForm->createView();

                $parameters['recentActivity'] = $this->getDoctrine()->getRepository(
                    ProcessLogEntry::class
                )->getLatestEntriesByLevel(
                    ProcessLogEntryLevelEnumType::NOTICE,
                    10,
                    $parameters['process']
                );
            }
        }

        return parent::render($view, $parameters, $response);
    }

    /**
     * @param Process $process
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createChangeProcessStatusForm(Process $process)
    {
        return $this->createFormBuilder($process)
            ->add('processStatus', EntityType::class, [
                'class' => ProcessStatus::class,
                'choices' => $process->getProcessType()->getProcessStatuses(),
                'label' => 'process.form.process_status',
                'label_attr' => ['class' => 'sr-only'],
                'placeholder' => 'process.form.change_process_status.placeholder',
                'attr' => ['class' => 'form-control-lg process-type-select'],
            ])
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'process.form.change_process_status.save',
                    'attr' => [
                        'style' => 'display: none',
                        'class' => 'btn-sm btn-primary mt-1',
                    ],
                ]
            )
            ->getForm();
    }

    /**
     * Redirect to route if the process is not editable.
     *
     * @param Process $process
     *   The process
     * @param string  $route
     *   The route to redirect to
     * @param array   $routeParams
     *   The parameters to the redirect
     *
     * @return RedirectResponse
     */
    public function redirectOnProcessComplete(Process $process, string $route, array $routeParams = [])
    {
        if (null !== $process->getCompletedAt()) {
            // @TODO: Fix translation.
            $this->addFlash('warning', 'The case is completed and can therefore not be edited.');

            return $this->redirectToRoute('', $routeParams);
        }
    }

    /**
     * @param $request
     * @param $changeProcessStatusForm
     */
    public function handleChangeProcessStatusForm($request, $changeProcessStatusForm)
    {
        $changeProcessStatusForm->handleRequest($request);
        if ($changeProcessStatusForm->isSubmitted() && $changeProcessStatusForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }
    }

    /**
     * @param string $route
     * @param array  $parameters
     * @param int    $status
     *
     * @return RedirectResponse
     */
    protected function redirectToReferer(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        // Check for referer in query string.
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            $referer = $request->query->get('referer');
            if (null !== $referer) {
                return $this->redirect($referer);
            }
        }

        return $this->redirectToRoute($route, $parameters, $status);
    }

    /**
     * @param string $path
     *
     * @return array
     */
    protected function getGlobalNavMenu(string $path = '/')
    {
        return $this->menuService->getGlobalNavMenu($path);
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;

class BaseController extends AbstractController
{
    protected $requestStack;
    protected $menuService;

    public function __construct(
        RequestStack $requestStack,
        MenuService $menuService
    ) {
        $this->requestStack = $requestStack;
        $this->menuService = $menuService;
    }

    /**
     * Render view.
     *
     * Attaches menu and quick links.
     *
     * @param string                                          $view
     * @param array                                           $parameters
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function render(
        string $view,
        array $parameters = [],
        Response $response = null
    ): Response {
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
        $parameters['globalMenuItems'] = $this->menuService->getGlobalNavMenu($path);

        // If this is a route beneath the proces/{id}/, attach changeProcessStatusForm.
        if (1 === preg_match('/^\/process\/[0-9]+/', $path) && isset($parameters['process'])) {
            $changeProcessStatusForm = $this->createChangeProcessStatusForm($parameters['process']);
            $this->handleChangeProcessStatusForm($request, $changeProcessStatusForm);

            $parameters['changeProcessStatusForm'] = $changeProcessStatusForm->createView();
        }

        return parent::render($view, $parameters, $response);
    }

    public function createChangeProcessStatusForm($process) {
        return $this->createFormBuilder($process)
            ->add(
                'processStatus',
                null,
                [
                    'label' => 'process.form.process_status',
                    'label_attr' => array('class'=>'sr-only'),
                    'placeholder' => 'process.form.change_process_status.placeholder',
                    'attr'=> array('class'=>'form-control-lg')
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'process.form.change_process_status.save',
                    'attr' => [
                        'style' => 'display: none',
                    ],
                ]
            )
            ->getForm();
    }

    public function handleChangeProcessStatusForm($request, $changeProcessStatusForm)
    {
        $changeProcessStatusForm->handleRequest($request);
        if ($changeProcessStatusForm->isSubmitted() && $changeProcessStatusForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }
    }
}

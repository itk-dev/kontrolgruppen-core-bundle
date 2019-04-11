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

        return parent::render($view, $parameters, $response);
    }
}

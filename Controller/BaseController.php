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
use Kontrolgruppen\CoreBundle\Twig\TwigExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    protected $requestStack;
    protected $translator;
    protected $twigExtension;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        TwigExtension $twigExtension
    ) {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->twigExtension = $twigExtension;
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

        // Set global nav items.
        $globalNavItems = [
            'dashboard' => $this->createGlobalNavItem(
                'dashboard',
                '/',
                'dashboard',
                ('/' === $path)
            ),
            'process' => $this->createGlobalNavItem(
                'process',
                '/process/',
                'process',
                (false !== $this->startsWith($path, '/process/'))
            ),
            'profile' => $this->createGlobalNavItem(
                'profile',
                '/profile/',
                'profile',
                (false !== $this->startsWith($path, '/profile/'))
            ),
            'users' => $this->createGlobalNavItem(
                'users',
                '/users/',
                'users',
                (false !== $this->startsWith($path, '/profile/'))
            ),
            'admin' => $this->createGlobalNavItem(
                'admin',
                '/admin/',
                'admin',
                (false !== $this->startsWith($path, '/admin/'))
            ),
        ];
        $parameters['globalMenuItems'] = $globalNavItems;

        return parent::render($view, $parameters, $response);
    }

    /**
     * Generate global_nav item.
     *
     * @param $itemName
     * @param $path
     * @param $icon
     * @param $active
     *
     * @return object
     */
    protected function createGlobalNavItem($itemName, $path, $iconName, $active)
    {
        return (object) [
            'name' => $this->translator->trans(
                'global_nav.menu_title.'.$itemName
            ),
            'icon' => $this->twigExtension->getIconClass($iconName),
            'tooltip' => $this->translator->trans(
                'global_nav.tooltip.'.$itemName
            ),
            'path' => $path,
            'active' => $active,
        ];
    }

    /**
     * Create menu item.
     *
     * @param $itemName
     * @param $path
     * @param $active
     *
     * @return array
     */
    protected function createMenuItem($itemName, $path, $active)
    {
        return [
            'name' => $this->translator->trans('menu.menu_title.'.$itemName),
            'path' => $path,
            'active' => $active,
        ];
    }

    /**
     * Tests if the haystack starts with the needle.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    protected function startsWith($haystack, $needle)
    {
        $length = \strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }
}

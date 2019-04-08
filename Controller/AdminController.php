<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController.
 *
 * @Route("/admin", name="admin")
 */
class AdminController extends BaseController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        $path = $this->requestStack->getCurrentRequest()->getPathInfo();

        // Set main menu items
        $menuItems = [
            'process_type' => [
                'name' => $this->translator->trans('process_type.menu_title'),
                'path' => '/process_type/',
                'active' => false !== $this->startsWith($path, '/process_type/'),
            ],
            'process_status' => [
                'name' => $this->translator->trans('process_status.menu_title'),
                'path' => '/process_status/',
                'active' => false !== $this->startsWith($path, '/process_status/'),
            ],
            'channel' => [
                'name' => $this->translator->trans('channel.menu_title'),
                'path' => '/channel/',
                'active' => false !== $this->startsWith($path, '/channel/'),
            ],
            'service' => [
                'name' => $this->translator->trans('service.menu_title'),
                'path' => '/service/',
                'active' => false !== $this->startsWith($path, '/service/'),
            ],
            'quickLinks' => [
                'name' => $this->translator->trans('quick_link.menu_title'),
                'path' => '/quick_link/',
                'active' => false !== $this->startsWith($path, '/quick_link/'),
            ],
        ];

        return $this->render(
            '@KontrolgruppenCore/admin/index.html.twig',
            [
                'menuItems' => $menuItems,
            ]
        );
    }
}

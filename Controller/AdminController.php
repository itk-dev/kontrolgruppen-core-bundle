<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 *
 * @package Kontrolgruppen\CoreBundle\Controller
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
                'active' => $this->startsWith($path, '/process_type/') != false,
            ],
            'process_status' => [
                'name' => $this->translator->trans('process_status.menu_title'),
                'path' => '/process_status/',
                'active' => $this->startsWith($path, '/process_status/') != false,
            ],
            'channel' => [
                'name' => $this->translator->trans('channel.menu_title'),
                'path' => '/channel/',
                'active' => $this->startsWith($path, '/channel/') != false,
            ],
            'service' => [
                'name' => $this->translator->trans('service.menu_title'),
                'path' => '/service/',
                'active' => $this->startsWith($path, '/service/') != false,
            ],
            'quickLinks' => [
                'name' => $this->translator->trans('quick_link.menu_title'),
                'path' => '/quick_link/',
                'active' => $this->startsWith($path, '/quick_link/') != false,
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

<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class BaseController extends AbstractController
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function baseRender($view, $parameters = null) {
        $parameters = $parameters ?: [];

        // Set quickLinks
        $quickLinks = $this->getDoctrine()->getRepository(QuickLink::class)->findAll();
        $parameters['quickLinks'] = $quickLinks;

        $request = $this->requestStack->getCurrentRequest();

        $path = $request->getPathInfo();
        $parameters['path'] = $path;

        // Set main menu items
        $menuItems = [
            'process' => [
                'name' => 'Process',
                'path' => '/process/',
                'active' => $this->startsWith($path, '/process/') != FALSE
            ],
            'channel' => [
                'name' => 'Channel',
                'path' => '/channel/',
                'active' => $this->startsWith($path, '/channel/') != FALSE
            ],
            'service' => [
                'name' => 'Service',
                'path' => '/service/',
                'active' => $this->startsWith($path, '/service/') != FALSE
            ],
            'quickLinks' => [
                'name' => 'Quick Links',
                'path' => '/quick_link/',
                'active' => $this->startsWith($path, '/quick_link/') != FALSE
            ]
        ];
        $parameters['menuItems'] = $menuItems;

        return $this->render($view, $parameters);
    }

    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

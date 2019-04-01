<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class BaseController extends AbstractController
{
    protected $requestStack;
    protected $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
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
                'name' => $this->translator->trans('process.menu_title'),
                'path' => '/process/',
                'active' => $this->startsWith($path, '/process/') != FALSE
            ],
            'process_status' => [
                'name' => $this->translator->trans('process_status.menu_title'),
                'path' => '/process_status/',
                'active' => $this->startsWith($path, '/process_status/') != FALSE
            ],
            'process_type' => [
                'name' => $this->translator->trans('process_type.menu_title'),
                'path' => '/process_type/',
                'active' => $this->startsWith($path, '/process_type/') != FALSE
            ],
            'channel' => [
                'name' => $this->translator->trans('channel.menu_title'),
                'path' => '/channel/',
                'active' => $this->startsWith($path, '/channel/') != FALSE
            ],
            'service' => [
                'name' => $this->translator->trans('service.menu_title'),
                'path' => '/service/',
                'active' => $this->startsWith($path, '/service/') != FALSE
            ],
            'quickLinks' => [
                'name' => $this->translator->trans('quick_link.menu_title'),
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

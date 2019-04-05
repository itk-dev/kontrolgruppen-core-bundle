<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    protected $requestStack;
    protected $translator;

    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    /**
     * Render view.
     *
     * Attaches menu and quick links.
     *
     * @param string $view
     * @param array $parameters
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(string $view, array $parameters = [], Response $response = NULL): Response {
        // Set reminders
        $reminders = $this->getDoctrine()->getRepository(Reminder::class)->findActiveUserReminders($this->getUser());
        $parameters['activeUserReminders'] = $reminders;

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
            'process_type' => [
                'name' => $this->translator->trans('process_type.menu_title'),
                'path' => '/process_type/',
                'active' => $this->startsWith($path, '/process_type/') != FALSE
            ],
            'process_status' => [
                'name' => $this->translator->trans('process_status.menu_title'),
                'path' => '/process_status/',
                'active' => $this->startsWith($path, '/process_status/') != FALSE
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

        $subMenuItems = [];

        if (preg_match('/\/process\/.+/', $path) != FALSE) {
            $process = $request->get('process');

            if (isset($process) && $process->getId() != null) {
                $subMenuItems['reminders'] = [
                    'name' => $this->translator->trans('reminder.menu_title'),
                    'path' => '/process/'.$process->getId().'/reminder',
                    'active' => preg_match('/\/process\/\d+\/reminder\/.*/', $path) != FALSE
                ];
            }
        }

        $parameters['subMenuItems'] = $subMenuItems;

        return parent::render($view, $parameters, $response);
    }

    /**
     * Tests if the haystack starts with the needle.
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}

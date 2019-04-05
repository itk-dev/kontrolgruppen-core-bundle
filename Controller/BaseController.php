<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
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
     * @param string $view
     * @param array $parameters
     * @param \Symfony\Component\HttpFoundation\Response|null $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(string $view, array $parameters = [], Response $response = NULL): Response {
        // Set quickLinks
        $quickLinks = $this->getDoctrine()->getRepository(QuickLink::class)->findAll();
        $parameters['quickLinks'] = $quickLinks;

        $request = $this->requestStack->getCurrentRequest();

        $path = $request->getPathInfo();
        $parameters['path'] = $path;

        // Set global menu items
        $globalMenuItems = [
          'dashboard' => [
              'name' => $this->translator->trans('dashboard.menu_title'),
              'path' => '/',
              'active' => $this->startsWith($path, '/') != FALSE,
              'icon' => 'fa-tachometer-alt',
              'tooltip' => $this->translator->trans('dashboard.tooltip')
          ],
          'process' => [
            'name' => $this->translator->trans('process.menu_title'),
            'path' => '/process/',
            'active' => $this->startsWith($path, '/process/') != FALSE,
            'icon' => 'fa-tasks',
            'tooltip' => $this->translator->trans('process.tooltip')
          ],
          'my_page' => [
            'name' => $this->translator->trans('my_page.menu_title'),
            'path' => '/profile/',
            'active' => $this->startsWith($path, '/profile/') != FALSE,
            'icon' => 'fa-id-card',
            'tooltip' => $this->translator->trans('my_page.menu_title')
          ],
          'users' => [
            'name' => $this->translator->trans('users.menu_title'),
            'path' => '/users/',
            'active' => $this->startsWith($path, '/users/') != FALSE,
            'icon' => 'fa-users-cog',
            'tooltip' => $this->translator->trans('users.tooltip')
          ],
          'settings' => [
            'name' => $this->translator->trans('settings.menu_title'),
            'path' => '/settings/',
            'active' => $this->startsWith($path, '/settings/') != FALSE,
            'icon' => 'fa-cog',
            'tooltip' => $this->translator->trans('settings.tooltip')
          ],
        ];
        $parameters['globalMenuItems'] = $globalMenuItems;

        $menuItems = [
        ];
        $parameters['menuItems'] = $menuItems;

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

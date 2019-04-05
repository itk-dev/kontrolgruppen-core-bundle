<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends BaseController
{
    /**
     * @Route("/settings", name="settings")
     */

    public function index()
    {

        // Set settings menu items
      //   $settingsMenuItems = [
      //     'process_type' => [
      //         'name' => $this->translator->trans('process_type.menu_title'),
      //         'path' => '/process_type/',
      //         'active' => $this->startsWith($path, '/process_type/') != FALSE
      //     ],
      //     'process_status' => [
      //         'name' => $this->translator->trans('process_status.menu_title'),
      //         'path' => '/process_status/',
      //         'active' => $this->startsWith($path, '/process_status/') != FALSE
      //     ],
      //     'channel' => [
      //         'name' => $this->translator->trans('channel.menu_title'),
      //         'path' => '/channel/',
      //         'active' => $this->startsWith($path, '/channel/') != FALSE
      //     ],
      //     'service' => [
      //         'name' => $this->translator->trans('service.menu_title'),
      //         'path' => '/service/',
      //         'active' => $this->startsWith($path, '/service/') != FALSE
      //     ],
      //     'quickLinks' => [
      //         'name' => $this->translator->trans('quick_link.menu_title'),
      //         'path' => '/quick_link/',
      //         'active' => $this->startsWith($path, '/quick_link/') != FALSE
      //     ]
      //   ];
      // $parameters['settingsMenuItems'] = $settingsMenuItems;
      //return parent::render($view, $parameters, $response);

        return $this->render('settings/index.html.twig');
    }
}

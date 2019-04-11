<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Service\MenuService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController.
 *
 * @Route("/admin")
 */
class AdminController extends BaseController
{
    /**
     * @Route("/", name="admin_index")
     */
    public function index(Request $request, MenuService $menuService)
    {
        return $this->render(
            '@KontrolgruppenCore/admin/index.html.twig',
            [
                'menuItems' => $menuService->getAdminMenu($request->getPathInfo()),
            ]
        );
    }
}

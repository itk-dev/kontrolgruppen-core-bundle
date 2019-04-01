<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends BaseController
{
    /**
     * @Route("", name="main")
     */
    public function index()
    {
        return $this->baseRender('main/index.html.twig');
    }
}

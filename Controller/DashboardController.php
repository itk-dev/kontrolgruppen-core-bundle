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
        return $this->render('@KontrolgruppenCore/main/index.html.twig');
    }
}

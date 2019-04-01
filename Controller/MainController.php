<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

class MainController extends BaseController
{
    /**
     * @Route("", name="main")
     */
    public function index()
    {
        return $this->baseRender('main/index.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->baseRender('main/test.html.twig', []);
    }
}

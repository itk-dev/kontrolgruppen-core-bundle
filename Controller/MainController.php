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
        return $this->baseRender('@KontrolgruppenCoreBundle/main/index.html.twig');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->baseRender('@KontrolgruppenCoreBundle/main/test.html.twig', []);
    }
}

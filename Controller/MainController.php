<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("", name="main")
     */
    public function index()
    {
        return $this->render('@KontrolgruppenCoreBundle/main/index.html.twig', []);
    }

    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render('main/test.html.twig', []);
    }
}

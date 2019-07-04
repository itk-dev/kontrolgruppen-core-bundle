<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Service\EconomyService;

/**
 * Class EconomyController.
 *
 * @Route("/process/{process}/revenue")
 */
class RevenueController extends BaseController
{
    /**
     * @Route("/", name="economy_revenue")
     */
    public function revenue(Request $request, Process $process, EconomyService $economyService)
    {
        $parameters = [];

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'revenue.calculate_button',
        ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameters['revenue'] = $economyService->calculateRevenue($process);
        }

        $parameters['form'] = $form->createView();
        $parameters['menuItems'] = $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;

        return $this->render(
            '@KontrolgruppenCore/revenue/revenue.html.twig',
            $parameters
        );
    }
}

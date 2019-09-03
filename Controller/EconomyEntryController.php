<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Kontrolgruppen\CoreBundle\Form\BaseEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\RevenueServiceEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\ServiceEconomyEntryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Symfony\Component\HttpFoundation\RequestStack;
use Kontrolgruppen\CoreBundle\Service\MenuService;

/**
 * @Route("/process/{process}/economy_entry")
 */
class EconomyEntryController extends BaseController
{
    private $economyController;

    /**
     * EconomyEntryController constructor.
     */
    public function __construct(
        RequestStack $requestStack,
        MenuService $menuService,
        EconomyController $economyController
    ) {
        parent::__construct($requestStack, $menuService);
        $this->economyController = $economyController;
    }

    /**
     * @Route("/{id}/edit", name="economy_entry_edit", methods={"GET","POST"})
     */
    public function edit(Process $process, EconomyEntry $economyEntry, Request $request): Response
    {
        if ($economyEntry instanceof ServiceEconomyEntry) {
            $form = $this->createForm(ServiceEconomyEntryType::class, $economyEntry);
        } else {
            $form = $this->createForm(BaseEconomyEntryType::class, $economyEntry);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'economy_show',
                [
                    'process' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/economy_entry/edit.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'economyEntry' => $economyEntry,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="economy_entry_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EconomyEntry $economyEntry, Process $process): Response
    {
        if ($this->isCsrfTokenValid('delete'.$economyEntry->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($economyEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('economy_show', ['process' => $process]);
    }

    /**
     * @Route("/{id}/storeRevenue", name="economy_entry_store_revenue", methods={"POST"})
     */
    public function storeRevenueForEconomyEntry(Request $request, Process $process, ServiceEconomyEntry $serviceEconomyEntry)
    {
        $form = $this->container->get('form.factory')->createNamedBuilder(
            'revenue_entry_'.$serviceEconomyEntry->getId(),
            RevenueServiceEconomyEntryType::class,
            $serviceEconomyEntry
        )->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'economy_show',
                [
                    'process' => $process->getId(),
                ]
            );
        }

        echo $serviceEconomyEntry->getId();

        return new Response('It did not work at all!!!');
    }
}

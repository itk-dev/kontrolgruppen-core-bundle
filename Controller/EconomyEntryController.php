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
use Kontrolgruppen\CoreBundle\Entity\IncomeEconomyEntry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Kontrolgruppen\CoreBundle\Form\BaseEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\IncomeEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\ServiceEconomyEntryType;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process/{process}/economy_entry")
 */
class EconomyEntryController extends BaseController
{
    private $economyController;

    /**
     * EconomyEntryController constructor.
     *
     * @param RequestStack      $requestStack
     * @param MenuService       $menuService
     * @param EconomyController $economyController
     */
    public function __construct(RequestStack $requestStack, MenuService $menuService, EconomyController $economyController)
    {
        parent::__construct($requestStack, $menuService);
        $this->economyController = $economyController;
    }

    /**
     * @Route("/{id}/edit", name="economy_entry_edit", methods={"GET","POST"})
     *
     * @param Process      $process
     * @param EconomyEntry $economyEntry
     * @param Request      $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Process $process, EconomyEntry $economyEntry, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // Redirect to show if process is completed.
        $this->redirectOnProcessComplete($process, 'economy_show', ['process' => $process->getId()]);

        if ($economyEntry instanceof ServiceEconomyEntry) {
            $form = $this->createForm(ServiceEconomyEntryType::class, $economyEntry);
        } elseif ($economyEntry instanceof IncomeEconomyEntry) {
            $form = $this->createForm(IncomeEconomyEntryType::class, $economyEntry);
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
     *
     * @param Request      $request
     * @param EconomyEntry $economyEntry
     * @param Process      $process
     *
     * @return Response
     */
    public function delete(Request $request, EconomyEntry $economyEntry, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // Redirect to show if process is completed.
        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('economy_show', [
                'process' => $process->getId(),
            ]);
        }

        if ($this->isCsrfTokenValid('delete'.$economyEntry->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($economyEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('economy_show', ['process' => $process]);
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Kontrolgruppen\CoreBundle\Form\EconomyEntryType;

/**
 * Class EconomyController.
 *
 * @Route("/process/{process}/economy")
 */
class EconomyController extends BaseController
{
    /**
     * @Route("/", name="economy_show")
     */
    public function show(Request $request, Process $process, EconomyEntryRepository $economyEntryRepository)
    {
        $parameters = $this->handleEconomyEntryFormRequest($request, $process);

        if ($parameters instanceof RedirectResponse) {
            return $parameters;
        }

        $parameters['menuItems'] =  $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;
        $parameters['economyEntries'] = $economyEntryRepository->findBy(['process' => $process]);

        return $this->render(
            '@KontrolgruppenCore/economy/show.html.twig',
            $parameters
        );
    }

    /**
     * Handles the economy entry form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse Parameters for the form, or redirects on success.
     */
    private function handleEconomyEntryFormRequest(Request $request, Process $process) {
        $economyEntry = new EconomyEntry();
        $economyEntry->setProcess($process);
        $form = $this->createForm(EconomyEntryType::class, $economyEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($economyEntry);
            $entityManager->flush();

            return $this->redirectToRoute('economy_show', ['process' => $process]);
        }

        return [
            'economy_entry' => $economyEntry,
            'economy_entry_form' => $form->createView(),
        ];
    }
}

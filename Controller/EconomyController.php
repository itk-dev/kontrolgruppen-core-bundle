<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\BaseEconomyEntry;
use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Kontrolgruppen\CoreBundle\Entity\IncomeEconomyEntry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Kontrolgruppen\CoreBundle\Form\BaseEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\EconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\IncomeEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\RevenueServiceEconomyEntryType;
use Kontrolgruppen\CoreBundle\Form\ServiceEconomyEntryType;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $parameters = [];

        $canEdit = $this->isGranted('edit', $process) && $process->getCompletedAt() === null;
        $parameters['canEdit'] = $canEdit;

        if ($canEdit) {
            // Check for result of type form.
            $formResult = $this->handleEconomyEntryFormRequest($request, $process);
            $parameters['economy_entry_form'] = $formResult['form'];

            // Handle entity form.
            $chosenType = isset($formResult['chosenType']) ? $formResult['chosenType'] : null;
            $entityFormResult = $this->handleEntityForm($request, $process, $chosenType, $parameters);

            if ($entityFormResult instanceof RedirectResponse) {
                return $entityFormResult;
            }

            $parameters['collapse_economy_entry_form'] = !isset($formResult['submitted']) || !$formResult['submitted'];
        }

        $parameters['menuItems'] = $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;

        $parameters['economyEntriesService'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::SERVICE]);
        $parameters['economyEntriesIncome'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::INCOME]);
        $parameters['economyEntriesAccount'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::ACCOUNT]);

        $parameters['revenueForms'] = [];
        $revenueFormErrors = [];
        foreach ($parameters['economyEntriesService'] as $serviceEconomyEntry) {
            $options = [];

            if (!$canEdit) {
                $options['disabled'] = true;
            }

            $revenueForm = $this->container->get('form.factory')->createNamedBuilder(
                'revenue_entry_'.$serviceEconomyEntry->getId(),
                RevenueServiceEconomyEntryType::class,
                $serviceEconomyEntry,
                $options
            )->getForm();

            if ($canEdit) {
                $revenueForm->handleRequest($request);

                if ($revenueForm->isSubmitted() && $revenueForm->isValid()) {
                    $this->getDoctrine()->getManager()->flush();
                }

                if ($revenueForm->isSubmitted() && !$revenueForm->isValid()) {
                    $revenueFormErrors[] = $revenueForm->getName();
                }
            }

            $parameters['revenueForms'][] = $revenueForm->createView();
        }

        // Forms are submitted in an ajax request, so if anything bad happens, we need to send an answer
        // that can be handled.
        if (!empty($revenueFormErrors)) {
            $response = new JsonResponse($revenueFormErrors);
            $response->setStatusCode(400);

            return $response;
        }

        return $this->render(
            '@KontrolgruppenCore/economy/show.html.twig',
            $parameters
        );
    }

    /**
     * @param $chosenType
     * @param $parameters
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleEntityForm(Request $request, Process $process, $chosenType, &$parameters)
    {
        // Decide if a type has been chosen.
        if (!$chosenType && $request->request->has('base_economy_entry')) {
            $chosenType = $request->request->get('base_economy_entry')['type'];
        } elseif (!$chosenType && $request->request->has('service_economy_entry')) {
            $chosenType = $request->request->get('service_economy_entry')['type'];
        } elseif (!$chosenType && $request->request->has('income_economy_entry')) {
            $chosenType = $request->request->get('income_economy_entry')['type'];
        }

        // Add given form if a type has been chosen.
        if ($chosenType) {
            if (EconomyEntryEnumType::SERVICE === $chosenType) {
                $economyEntry = new ServiceEconomyEntry();
                $economyEntry->setType($chosenType);
                $form = $this->createForm(ServiceEconomyEntryType::class, $economyEntry);
            } elseif (EconomyEntryEnumType::INCOME === $chosenType) {
                $economyEntry = new IncomeEconomyEntry();
                $economyEntry->setType($chosenType);
                $form = $this->createForm(IncomeEconomyEntryType::class, $economyEntry);
            } else {
                $economyEntry = new BaseEconomyEntry();
                $economyEntry->setType($chosenType);
                $form = $this->createForm(BaseEconomyEntryType::class, $economyEntry);
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $economyEntry->setProcess($process);
                $entityManager->persist($economyEntry);
                $entityManager->flush();

                return $this->redirectToRoute('economy_show', ['process' => $process]);
            }

            $parameters['form'] = $form->createView();
        }
    }

    /**
     * Handles the economy entry form.
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse parameters for the form, or redirects on success
     */
    public function handleEconomyEntryFormRequest(Request $request, Process $process)
    {
        $economyEntry = new EconomyEntry();
        $economyEntry->setProcess($process);
        $form = $this->createForm(EconomyEntryType::class, $economyEntry);
        $form->handleRequest($request);

        $result = [
            'economy_entry' => $economyEntry,
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $result['chosenType'] = $economyEntry->getType();
            $result['submitted'] = true;
        }

        return $result;
    }
}

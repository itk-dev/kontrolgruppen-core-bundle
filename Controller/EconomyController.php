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
use Kontrolgruppen\CoreBundle\Form\RevenueType;
use Kontrolgruppen\CoreBundle\Form\ServiceEconomyEntryType;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
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
     *
     * @param Request                $request
     * @param Process                $process
     * @param EconomyEntryRepository $economyEntryRepository
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function show(Request $request, Process $process, EconomyEntryRepository $economyEntryRepository)
    {
        $parameters = [];

        $canEdit = $this->isGranted('edit', $process) && null === $process->getCompletedAt();
        $parameters['canEdit'] = $canEdit;

        $parameters['showOnlyAdminCanEditWarning'] = null !== $process->getLastNetCollectiveSum() && null === $process->getCompletedAt();

        // If the user can edit, handle forms.
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

            if (!isset($parameters['collapse_economy_entry_form'])) {
                $parameters['collapse_economy_entry_form'] = !isset($formResult['submitted']) || !$formResult['submitted'];
            }
        }

        $parameters['menuItems'] = $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;

        $parameters['economyEntriesService'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::SERVICE]);
        $parameters['economyEntriesIncome'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::INCOME]);
        $parameters['economyEntriesAccount'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::ACCOUNT]);

        $services = array_reduce($parameters['economyEntriesService'], function ($carry, ServiceEconomyEntry $element) {
            if (null !== $element->getService()) {
                $carry[$element->getService()->getId()] = $element->getService();
            }

            return $carry;
        }, []);
        $parameters['services'] = $services;

        $options = [];
        if (!$canEdit) {
            $options = ['disabled' => true];
        }

        $revenueForm = $this->createForm(RevenueType::class, $process, $options);

        $revenueForm->handleRequest($request);

        if ($revenueForm->isSubmitted()) {
            if ($revenueForm->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
            } else {
                $errors = $revenueForm->getErrors();

                foreach ($errors as $error) {
                    $this->addFlash('danger', $error->getMessage());
                }
            }
        }

        $parameters['revenueForm'] = $revenueForm->createView();

        return $this->render(
            '@KontrolgruppenCore/economy/show.html.twig',
            $parameters
        );
    }

    /**
     * @param Request $request
     * @param Process $process
     * @param         $chosenType
     * @param         $parameters
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

            if ($form->isSubmitted() && !$form->isValid()) {
                $parameters['collapse_economy_entry_form'] = false;
            }

            $parameters['form'] = $form->createView();
        }
    }

    /**
     * Handles the economy entry form.
     *
     * @param Request $request
     * @param Process $process
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

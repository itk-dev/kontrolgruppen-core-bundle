<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Service;
use Kontrolgruppen\CoreBundle\Form\ChangeNetDefaultValueType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/change_net_default_value")
 */
class ChangeNetDefaultValueController extends BaseController
{
    /**
     * @Route("/", name="change_net_default_value_index", methods={"GET", "POST"})
     *
     * @param Request             $request
     * @param ProcessRepository   $processRepository
     * @param TranslatorInterface $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ProcessRepository $processRepository, TranslatorInterface $translator)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ChangeNetDefaultValueType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            /** @var Service $service */
            $service = $formData['service'];
            $newNetValue = $formData['value'];

            $entityManager = $this->getDoctrine()->getManager();

            $processes = $processRepository->findCompletedByService($service);
            $numberOfAffectedProcesses = 0;
            foreach ($processes as $process) {
                if ($newNetValue !== $process->getLockedNetValue()) {
                    $process->setLockedNetValue($newNetValue);

                    $entityManager->persist($process);
                    ++$numberOfAffectedProcesses;
                }
            }

            $entityManager->flush();
            $this->addFlash('info', $translator->trans(
                'change_net_default_value.index.flash',
                ['%affected_processes%' => $numberOfAffectedProcesses]
            ));

            return $this->redirectToRoute('change_net_default_value_index');
        }

        return $this->render('change_net_default_value/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'form' => $form->createView(),
        ]);
    }
}

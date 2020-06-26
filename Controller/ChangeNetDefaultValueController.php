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
use Kontrolgruppen\CoreBundle\Repository\LockedNetValueRepository;
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
     * @param Request                  $request
     * @param LockedNetValueRepository $lockedNetValueRepository
     * @param TranslatorInterface      $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, LockedNetValueRepository $lockedNetValueRepository, TranslatorInterface $translator)
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

            $existingLockedNetValues = $lockedNetValueRepository->findBy([
                'service' => $service,
            ]);

            $affectedProcesses = [];
            foreach ($existingLockedNetValues as $lockedNetValue) {
                if ($lockedNetValue->getValue() !== $newNetValue) {
                    $lockedNetValue->setValue($newNetValue);
                    $entityManager->persist($lockedNetValue);

                    if (!\in_array($lockedNetValue->getProcess(), $affectedProcesses)) {
                        $affectedProcesses[] = $lockedNetValue->getProcess();
                    }
                }
            }

            $entityManager->flush();

            if (!empty($affectedProcesses)) {
                $this->addFlash('raw-info', $this->renderView(
                    '@KontrolgruppenCore/change_net_default_value/affected_processes_message.html.twig',
                    ['affectedProcesses' => $affectedProcesses]
                ));
            } else {
                $this->addFlash('info', $translator->trans('change_net_default_value.index.none_flash'));
            }

            return $this->redirectToRoute('change_net_default_value_index');
        }

        return $this->render('@KontrolgruppenCore/change_net_default_value/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'form' => $form->createView(),
        ]);
    }
}

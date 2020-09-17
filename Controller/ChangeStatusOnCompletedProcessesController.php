<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Kontrolgruppen\CoreBundle\Form\ChangeStatusOnCompletedProcessesType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/change_status_on_completed_processes")
 */
class ChangeStatusOnCompletedProcessesController extends BaseController
{
    /**
     * @Route("/", name="change_status_on_completed_processes", methods={"GET", "POST"})
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

        $affectedProcesses = $processRepository->findCompletedWithNoStatus();
        $form = $this->createForm(ChangeStatusOnCompletedProcessesType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            /** @var ProcessStatus $processStatus */
            $processStatus = $formData['processStatus'];

            if (empty($processStatus)) {
                $this->addFlash(
                    'danger',
                    $translator->trans('change_status_on_completed_processes.index.empty_process_status_flash')
                );

                return $this->redirectToRoute('change_status_on_completed_processes');
            }

            $entityManager = $this->getDoctrine()->getManager();

            foreach ($affectedProcesses as $affectedProcess) {
                $affectedProcess->setProcessStatus($processStatus);
                $entityManager->persist($affectedProcess);
            }

            $entityManager->flush();

            $flashMessage = !empty($affectedProcess)
                ? $translator->trans('change_status_on_completed_processes.index.status_changed_flash', ['%number_of_affected%' => \count($affectedProcesses)])
                : $translator->trans('change_status_on_completed_processes.index.none_flash')
            ;

            $this->addFlash('info', $flashMessage);

            return $this->redirectToRoute('change_status_on_completed_processes');
        }

        return $this->render('@KontrolgruppenCore/change_status_on_completed_processes/index.html.twig', [
            'controller_name' => 'ChangeStatusOnCompletedProcessesController',
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'number_of_affected_processes' => \count($affectedProcesses),
            'form' => $form->createView(),
        ]);
    }
}

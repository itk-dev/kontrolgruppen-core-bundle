<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\Client;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Form\ClientType;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process/{process}/client")
 */
class ClientController extends BaseController
{
    /**
     * @Route("/", name="client_show", methods={"GET","POST"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param CprServiceInterface $cprService
     * @param LoggerInterface     $logger
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Process $process, CprServiceInterface $cprService, LoggerInterface $logger): Response
    {
        $client = $process->getClient();

        $changeProcessStatusForm = $this->createChangeProcessStatusForm($process);
        $this->handleChangeProcessStatusForm($request, $changeProcessStatusForm);

        // Make sure a client has been created for the process.
        if (!isset($client)) {
            $client = new Client();

            try {
                $client = $cprService->populateClient(new Cpr($process->getClientCPR()), $client);
            } catch (CprException $e) {
                $logger->error($e);
            }

            $client->setProcess($process);
            $process->setClient($client);

            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();
        }

        $newInfoAvailable = false;

        if ($this->isGranted('edit', $process)) {
            try {
                $newInfoAvailable = $cprService->isNewClientInfoAvailable(new Cpr($process->getClientCPR()), $client);
            } catch (CprException $e) {
                $logger->error($e);
            }
        }

        return $this->render('@KontrolgruppenCore/client/show.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'client' => $process->getClient(),
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'changeProcessStatusForm' => $changeProcessStatusForm->createView(),
            'process' => $process,
            'newClientInfoAvailable' => $newInfoAvailable,
        ]);
    }

    /**
     * @Route("/edit", name="client_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // Redirect to show if process is completed.
        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        $client = $process->getClient();

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/client/edit.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'client' => $client,
            'form' => $form->createView(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/update", name="client_update", methods={"GET"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param CprServiceInterface $cprService
     * @param LoggerInterface     $logger
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function update(Request $request, Process $process, CprServiceInterface $cprService, LoggerInterface $logger, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $client = $process->getClient();

        try {
            $client = $cprService->populateClient(new Cpr($process->getClientCPR()), $client);
            $this->addFlash('success', $translator->trans('client.show.client_updated'));
        } catch (CprException $e) {
            $this->addFlash('danger', $translator->trans('client.show.client_not_updated'));
            $logger->error($e);
        }

        $this->getDoctrine()->getManager()->persist($client);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('client_show', ['process' => $process]);
    }
}

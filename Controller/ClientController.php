<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Client;
use Kontrolgruppen\CoreBundle\Form\ClientType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * @Route("/process/{process}/client")
 */
class ClientController extends BaseController
{
    /**
     * @Route("/", name="client_show", methods={"GET","POST"})
     */
    public function show(Request $request, Process $process): Response
    {
        $client = $process->getClient();

        $changeProcessStatusForm = $this->createChangeProcessStatusForm($process);
        $this->handleChangeProcessStatusForm($request, $changeProcessStatusForm);

        // Make sure a client has been created for the process.
        if (!isset($client)) {
            $client = new Client();
            $client->setProcess($process);
            $process->setClient($client);

            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('@KontrolgruppenCore/client/show.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'client' => $process->getClient(),
            'changeProcessStatusForm' => $changeProcessStatusForm->createView(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/edit", name="client_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process): Response
    {
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
            'client' => $client,
            'form' => $form->createView(),
            'process' => $process,
        ]);
    }
}

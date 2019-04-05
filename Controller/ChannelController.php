<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Channel;
use Kontrolgruppen\CoreBundle\Form\ChannelType;
use Kontrolgruppen\CoreBundle\Repository\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/channel")
 */
class ChannelController extends BaseController
{
    /**
     * @Route("/", name="channel_index", methods={"GET"})
     */
    public function index(ChannelRepository $channelRepository): Response
    {
        return $this->render('@KontrolgruppenCore/channel/index.html.twig', [
            'channels' => $channelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="channel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($channel);
            $entityManager->flush();

            return $this->redirectToRoute('channel_index');
        }

        return $this->render('@KontrolgruppenCore/channel/new.html.twig', [
            'channel' => $channel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="channel_show", methods={"GET"})
     */
    public function show(Channel $channel): Response
    {
        return $this->render('@KontrolgruppenCore/channel/show.html.twig', [
            'channel' => $channel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="channel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Channel $channel): Response
    {
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('channel_index', [
                'id' => $channel->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/channel/edit.html.twig', [
            'channel' => $channel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="channel_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Channel $channel): Response
    {
        if ($this->isCsrfTokenValid('delete'.$channel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($channel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('channel_index');
    }
}

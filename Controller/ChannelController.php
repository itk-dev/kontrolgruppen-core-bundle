<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Channel;
use Kontrolgruppen\CoreBundle\Form\ChannelType;
use Kontrolgruppen\CoreBundle\Repository\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/channel")
 */
class ChannelController extends BaseController
{
    /**
     * @Route("/", name="channel_index", methods={"GET"})
     *
     * @param Request           $request
     * @param ChannelRepository $channelRepository
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, ChannelRepository $channelRepository): Response
    {
        return $this->render('@KontrolgruppenCore/channel/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'channels' => $channelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="channel_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'channel' => $channel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="channel_show", methods={"GET"})
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Channel $channel): Response
    {
        return $this->render('@KontrolgruppenCore/channel/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'channel' => $channel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="channel_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'channel' => $channel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="channel_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Channel $channel
     *
     * @return Response
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

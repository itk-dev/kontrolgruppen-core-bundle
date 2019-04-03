<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\QuickLink;
use Kontrolgruppen\CoreBundle\Form\QuickLinkType;
use Kontrolgruppen\CoreBundle\Repository\QuickLinkRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quick_link")
 */
class QuickLinkController extends BaseController
{
    /**
     * @Route("/", name="quick_link_index", methods={"GET"})
     */
    public function index(QuickLinkRepository $quickLinkRepository): Response
    {
        return $this->render('quick_link/index.html.twig', [
            'quick_links' => $quickLinkRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quick_link_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quickLink = new QuickLink();
        $form = $this->createForm(QuickLinkType::class, $quickLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setCreatedValues($quickLink);
            $this->setUpdatedValues($quickLink);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quickLink);
            $entityManager->flush();

            return $this->redirectToRoute('quick_link_index');
        }

        return $this->render('quick_link/new.html.twig', [
            'quick_link' => $quickLink,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quick_link_show", methods={"GET"})
     */
    public function show(QuickLink $quickLink): Response
    {
        return $this->render('quick_link/show.html.twig', [
            'quick_link' => $quickLink,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quick_link_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, QuickLink $quickLink): Response
    {
        $form = $this->createForm(QuickLinkType::class, $quickLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setUpdatedValues($quickLink);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quick_link_index', [
                'id' => $quickLink->getId(),
            ]);
        }

        return $this->render('quick_link/edit.html.twig', [
            'quick_link' => $quickLink,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quick_link_delete", methods={"DELETE"})
     */
    public function delete(Request $request, QuickLink $quickLink): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quickLink->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quickLink);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quick_link_index');
    }
}

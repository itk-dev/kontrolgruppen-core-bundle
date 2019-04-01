<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Form\ProcessTypeType;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process_type")
 */
class ProcessTypeController extends BaseController
{
    /**
     * @Route("/", name="process_type_index", methods={"GET"})
     */
    public function index(ProcessTypeRepository $processTypeRepository): Response
    {
        return $this->baseRender('process_type/index.html.twig', [
            'process_types' => $processTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="process_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $processType = new ProcessType();
        $form = $this->createForm(ProcessTypeType::class, $processType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $processType->setCreatedAt(new \DateTime());
            $processType->setUpdatedAt(new \DateTime());
            $processType->setCreatedBy($this->getUser());
            $processType->setUpdatedBy($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($processType);
            $entityManager->flush();

            return $this->redirectToRoute('process_type_index');
        }

        return $this->baseRender('process_type/new.html.twig', [
            'process_type' => $processType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_type_show", methods={"GET"})
     */
    public function show(ProcessType $processType): Response
    {
        return $this->baseRender('process_type/show.html.twig', [
            'process_type' => $processType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ProcessType $processType): Response
    {
        $form = $this->createForm(ProcessTypeType::class, $processType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('process_type_index', [
                'id' => $processType->getId(),
            ]);
        }

        return $this->baseRender('process_type/edit.html.twig', [
            'process_type' => $processType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ProcessType $processType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($processType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_type_index');
    }
}

<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\CaseType;
use Kontrolgruppen\CoreBundle\Form\CaseTypeType;
use Kontrolgruppen\CoreBundle\Repository\CaseTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/case_type")
 */
class CaseTypeController extends BaseController
{
    /**
     * @Route("/", name="case_type_index", methods={"GET"})
     */
    public function index(CaseTypeRepository $caseTypeRepository): Response
    {
        return $this->baseRender('case_type/index.html.twig', [
            'case_types' => $caseTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="case_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $caseType = new CaseType();
        $form = $this->createForm(CaseTypeType::class, $caseType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($caseType);
            $entityManager->flush();

            return $this->redirectToRoute('case_type_index');
        }

        return $this->baseRender('case_type/new.html.twig', [
            'case_type' => $caseType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="case_type_show", methods={"GET"})
     */
    public function show(CaseType $caseType): Response
    {
        return $this->baseRender('case_type/show.html.twig', [
            'case_type' => $caseType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="case_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CaseType $caseType): Response
    {
        $form = $this->createForm(CaseTypeType::class, $caseType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('case_type_index', [
                'id' => $caseType->getId(),
            ]);
        }

        return $this->baseRender('case_type/edit.html.twig', [
            'case_type' => $caseType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="case_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CaseType $caseType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$caseType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($caseType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('case_type_index');
    }
}

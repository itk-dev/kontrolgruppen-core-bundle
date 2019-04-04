<?php

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Form\ProcessStatusType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Kontrolgruppen\CoreBundle\Entity\ProcessStatus;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

/**
 * @Route("/process")
 */
class ProcessController extends BaseController
{
    /**
     * @Route("/", name="process_index", methods={"GET"})
     */
    public function index(ProcessRepository $processRepository): Response
    {
        return $this->render('process/index.html.twig', [
            'processes' => $processRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="process_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $process = new Process();
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process->setCaseNumber($this->getNewCaseNumber());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($process);
            $entityManager->flush();

            return $this->redirectToRoute('process_index');
        }

        return $this->render('process/new.html.twig', [
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Process $process): Response
    {
        // @TODO: Limit the available process statuses based on selected process type.
        $form = $this->createFormBuilder($process)
            ->add('processStatus', null, [
                'label' => 'process.form.process_status',
            ])
            ->add('save', SubmitType::class, ['label' => 'process.form.change_process_status.save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setUpdatedValues($process);

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('process/show.html.twig', [
            'process' => $process,
            'process_type_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="process_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process): Response
    {
        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('process_index', [
                'id' => $process->getId(),
            ]);
        }

        return $this->render('process/edit.html.twig', [
            'process' => $process,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="process_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Process $process): Response
    {
        if ($this->isCsrfTokenValid('delete'.$process->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($process);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_index');
    }

    /**
     * Generate a new case number.
     *
     * @TODO: Move to service.
     *
     * @return string Case number of format YY-XXXX where YY is the year and XXXX an increasing counter.
     */
    private function getNewCaseNumber()
    {
        $casesInYear = $this->getDoctrine()->getRepository(Process::class)->findAllFromYear(date('Y'));
        $caseNumber = str_pad(count($casesInYear) + 1, 5, "0", STR_PAD_LEFT);

        return date('y').'-'.$caseNumber;
    }
}

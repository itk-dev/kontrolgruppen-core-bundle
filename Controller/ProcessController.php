<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Filter\ProcessFilterType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Kontrolgruppen\CoreBundle\Entity\Client;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;

/**
 * @Route("/process")
 */
class ProcessController extends BaseController
{
    /**
     * @Route("/", name="process_index", methods={"GET"})
     */
    public function index(Request $request, ProcessRepository $processRepository, FilterBuilderUpdaterInterface $lexikBuilderUpdater, PaginatorInterface $paginator): Response
    {
        $wildcard = $request->query->get('process_filter_wildcard');

        $form = $this->get('form.factory')->create(ProcessFilterType::class);
        $form->get('wildcard')->setData($wildcard);

        $results = [];

        $qb = null;

        if ($request->query->has($form->getName())) {
            // manually bind values from the request
            $form->submit($request->query->get($form->getName()));

            // initialize a query builder
            $qb = $processRepository->createQueryBuilder('e');

            // build the query from the given form object
            $lexikBuilderUpdater->addFilterConditions($form, $qb);
        }
        else {
            $qb = $processRepository->createQueryBuilder('e');

            // Handle wildcard input.
            if (null !== $wildcard) {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like('e.clientCPR', ':wildcard'),
                        $qb->expr()->like('e.caseNumber', ':wildcard')
                    )
                )
                    ->setParameter('wildcard',  '%'.$wildcard.'%');
            }
        }

        $query = $qb->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            10
        );

        return $this->render('@KontrolgruppenCore/process/index.html.twig', array(
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo()),
            'processes' => $results,
            'pagination' => $pagination,
            'form' => $form->createView(),
            'query' => $query,
        ));
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

            $conclusionClass = $process->getProcessType()->getConclusionClass();
            $conclusion = new $conclusionClass();
            $process->setConclusion($conclusion);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($process);

            $client = new Client();
            $client->setCpr($process->getClientCPR());
            $process->setClient($client);

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('process_index');
        }

        return $this->render('@KontrolgruppenCore/process/new.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
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
        // @TODO: Replace with javascript widget.
        $form = $this->createFormBuilder($process)
            ->add('processStatus', null, [
                'label' => 'process.form.process_status',
            ])
            ->add('save', SubmitType::class, ['label' => 'process.form.change_process_status.save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
        }

        // Latest journal entries.
        $latestDiaryEntries = $this->getDoctrine()->getRepository(JournalEntry::class)->getLatestDiaryEntries($process);
        $latestNoteEntries = $this->getDoctrine()->getRepository(JournalEntry::class)->getLatestNoteEntries($process);
        $latestInternalNoteEntries = $this->getDoctrine()->getRepository(JournalEntry::class)->getLatestInternalNoteEntries($process);

        return $this->render('@KontrolgruppenCore/process/show.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'process' => $process,
            'process_type_form' => $form->createView(),
            'latestDiaryEntries' => $latestDiaryEntries,
            'latestNoteEntries' => $latestNoteEntries,
            'latestInternalNoteEntries' => $latestInternalNoteEntries,
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

        return $this->render('@KontrolgruppenCore/process/edit.html.twig', [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
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
     * @return string case number of format YY-XXXX where YY is the year and XXXX an increasing counter
     */
    private function getNewCaseNumber()
    {
        $casesInYear = $this->getDoctrine()->getRepository(Process::class)->findAllFromYear(date('Y'));
        $caseNumber = str_pad(\count($casesInYear) + 1, 5, '0', STR_PAD_LEFT);

        return date('y').'-'.$caseNumber;
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Account;
use Kontrolgruppen\CoreBundle\Form\AccountType;
use Kontrolgruppen\CoreBundle\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/account")
 */
class AccountController extends BaseController
{
    /**
     * @Route("/", name="account_index", methods={"GET"})
     *
     * @param Request           $request
     * @param AccountRepository $accounts
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request, AccountRepository $accounts): Response
    {
        return $this->render('@KontrolgruppenCore/account/index.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'accounts' => $accounts->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="account_new", methods={"GET","POST"})
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
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($account);
            $entityManager->flush();

            return $this->redirectToRoute('account_index');
        }

        return $this->render('@KontrolgruppenCore/account/new.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="account_show", methods={"GET"})
     *
     * @param Request $request
     * @param Account $account
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Account $account): Response
    {
        return $this->render('@KontrolgruppenCore/account/show.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'account' => $account,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="account_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Account $account
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Account $account): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('account_index', [
                'id' => $account->getId(),
            ]);
        }

        return $this->render('@KontrolgruppenCore/account/edit.html.twig', [
            'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
            'account' => $account,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="account_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Account $account
     *
     * @return Response
     */
    public function delete(Request $request, Account $account): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($account);
            $entityManager->flush();
        }

        return $this->redirectToRoute('account_index');
    }
}

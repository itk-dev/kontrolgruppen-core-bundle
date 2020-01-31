<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\BIExport;
use Kontrolgruppen\CoreBundle\Export\Manager;
use Kontrolgruppen\CoreBundle\Repository\BIExportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BusinessIntelligenceController.
 *
 * @Route("/bi", name="bi_")
 */
class BIController extends BaseController
{
    /**
     * @Route("/", name="index")
     *
     * @param BIExportRepository            $repository
     * @param AuthorizationCheckerInterface $authorizationChecker
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(BIExportRepository $repository, AuthorizationCheckerInterface $authorizationChecker)
    {
        $exports = $repository->findBy([], ['createdAt' => 'DESC']);

        $parameters = [
            'exports' => $exports,
        ];

        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $parameters['delete_form'] = $this->createDeleteForm()->createView();
        }

        return $this->render('@KontrolgruppenCore/bi/index.html.twig', $parameters);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createDeleteForm()
    {
        return $this->createFormBuilder()
            ->setAction('bi_delete')
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @Route("/download/{export}", name="download")
     *
     * @param BIExport $export
     *
     * @return BinaryFileResponse
     */
    public function download(BIExport $export)
    {
        $filename = $export->getFilename();

        $name = basename($filename);
        $headers = [
            'content-type' => $this->getContentType($filename),
            'content-disposition' => 'attachment; filename="'.$name.'"',
        ];

        return new BinaryFileResponse($filename, 200, $headers);
    }

    /**
     * @Route("/delete/{export}", name="delete", methods={"DELETE"})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param BIExport            $export
     * @param Manager             $manager
     * @param TranslatorInterface $translator
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(BIExport $export, Manager $manager, TranslatorInterface $translator)
    {
        $result = $manager->deleteBIExport($export);
        if ($result) {
            $this->addFlash('success', $translator->trans('BIExport successfully deleted'));
        } else {
            $this->addFlash('danger', $translator->trans('Error deleting BIExport'));
        }

        return $this->redirectToReferer('bi_index');
    }

    /**
     * @param $filename
     *
     * @return string
     */
    private function getContentType($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        switch ($ext) {
            case 'csv':
                return 'text/csv';
            case 'json':
                return 'application/json';
        }

        return 'text/plain';
    }
}

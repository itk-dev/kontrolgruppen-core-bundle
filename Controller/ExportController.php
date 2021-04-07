<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Export\Manager;
use Kontrolgruppen\CoreBundle\Export\Reports\RevenueExport;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Kontrolgruppen\CoreBundle\Service\PhpSpreadsheetExportService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * Class ExportController.
 *
 * @Route("/admin/export", name="export_")
 */
class ExportController extends BaseController
{
    /** @var Manager */
    private $exportManager;

    /** @var FormFactoryInterface */
    private $formFactory;
    private $twig;

    /**
     * ExportController constructor.
     *
     * @param RequestStack         $requestStack
     * @param MenuService          $menuService
     * @param Manager              $exportManager
     * @param FormFactoryInterface $formFactory
     * @param Environment          $twig
     */
    public function __construct(RequestStack $requestStack, MenuService $menuService, Manager $exportManager, FormFactoryInterface $formFactory, Environment $twig)
    {
        parent::__construct($requestStack, $menuService);
        $this->exportManager = $exportManager;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function index(Request $request)
    {
        $exports = $this->getExports();
        $parameterForms = [];
        foreach ($exports as $name => $export) {
            $view = $this->buildParameterForm($export)->createView();
            $parameterForms[$name] = $view;
        }

        return $this->render(
            '@KontrolgruppenCore/export/index.html.twig',
            [
                'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
                'exports' => $exports,
                'parameterForms' => $parameterForms,
            ]
        );
    }

    /**
     * @Route(
     *   "/run.{_format}",
     *   name="run",
     *   defaults={"_format": "xlsx"},
     *   requirements={
     *     "_format": "xlsx|csv|html|pdf",
     *   }
     * )
     *
     * @param Request                     $request
     * @param                             $_format
     * @param PhpSpreadsheetExportService $exportService
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response|StreamedResponse
     *
     * @throws \Exception
     */
    public function run(Request $request, $_format, PhpSpreadsheetExportService $exportService)
    {
        // The exports created in this controller could be very large,
        // so to prevent the server from crashing due to exceeding the
        // maximum allowed execution time, we disable it.
        // @todo We should find a better way to handle this.
        // @todo Maybe in the batch processing part of the specific Export classes.
        set_time_limit(0);

        $exportKey = $request->get('export');
        $exportClass = null;
        foreach ($this->getExports() as $r) {
            if ($this->getExportKey($r) === $exportKey) {
                $exportClass = \get_class($r);
                break;
            }
        }
        if (null === $exportClass) {
            $this->addFlash('danger', 'Invalid export');

            return $this->redirectToRoute('export_index');
        }

        /** @var AbstractExport $export */
        $export = $this->exportManager->getExport($exportClass);
        $form = $this->buildParameterForm($export);
        // Use namespaced form values (cf. $this->buildParameterForm).
        $form->submit($request->get($form->getName()));
        $parameters = $form->getData();
        $filename = preg_replace('/[^a-z0-9_]/i', '-', $export->getFilename($parameters));

        try {
            $writer = $this->exportManager->run($export, $parameters, $_format);

            switch ($_format) {
                case 'csv':
                    $filename .= '.csv';
                    $contentType = 'text/csv';
                    break;
                case 'xlsx':
                    $filename .= '.xlsx';
                    $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                    break;
                case 'pdf':
                    $filename .= '.pdf';
                    $contentType = 'application/pdf';
                    break;
                case 'html':
                default:
                    // Extract body content.
                    $d = new \DOMDocument();
                    $mock = new \DOMDocument();
                    $output = $exportService->getOutputAsString($writer);
                    $d->loadHTML($output);
                    $body = $d->getElementsByTagName('body')->item(0);
                    foreach ($body->childNodes as $child) {
                        if (\XML_ELEMENT_NODE === $child->nodeType) {
                            if ('style' === $child->tagName ?? null) {
                                continue;
                            }

                            if ('table' === $child->tagName ?? null) {
                                $child->setAttribute('class', 'table table-responsive');
                            }
                        }

                        $mock->appendChild($mock->importNode($child, true));
                    }

                    if (RevenueExport::class === \get_class($export)) {
                        $extra = $this->twig->render('@KontrolgruppenCore/export/revenue_export.show.html.twig');
                    }

                    return $this->render('@KontrolgruppenCore/export/show.html.twig', [
                        'menuItems' => $this->menuService->getAdminMenu($request->getPathInfo()),
                        'table' => $mock->saveHTML(),
                        'extra' => $extra ?? null,
                    ]);
            }

            $response = $exportService->getOutputInStreamedResponse($writer);

            $response->headers->set('Content-Type', $contentType);
            $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Error during export: '.$exception->getMessage());

            return $this->redirectToRoute('export_index');
        }
    }

    /**
     * @return AbstractExport[]
     */
    private function getExports()
    {
        $exports = [];

        foreach ($this->exportManager->getExports() as $export) {
            $exports[$this->getExportKey($export)] = $export;
        }

        return $exports;
    }

    /**
     * @param AbstractExport $export
     *
     * @return string
     */
    private function getExportKey(AbstractExport $export)
    {
        return md5(\get_class($export));
    }

    /**
     * @param AbstractExport $export
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function buildParameterForm(AbstractExport $export)
    {
        $parameters = $export->getParameters();

        // Use a named builder to namespace the parameter form values.
        $builder = $this->formFactory->createNamedBuilder('parameters_'.$this->getExportKey($export));
        foreach ($parameters as $name => $config) {
            $type = $config['type'] ?? null;
            $typeOptions = $config['type_options'] ?? [];
            $builder->add($name, $type, $typeOptions);
        }

        return $builder->getForm();
    }
}

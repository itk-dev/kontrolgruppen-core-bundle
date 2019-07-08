<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Box\Spout\Common\Type;
use Box\Spout\Writer\Common\Creator\WriterFactory;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Export\Manager;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExportController.
 *
 * @Route("/export", name="export_")
 */
class ExportController extends BaseController
{
    /** @var \Kontrolgruppen\CoreBundle\Export\Manager */
    private $exportManager;

    public function __construct(
        RequestStack $requestStack,
        MenuService $menuService,
        Manager $exportManager
    ) {
        parent::__construct($requestStack, $menuService);
        $this->exportManager = $exportManager;
    }

    /**
     * @Route("/", name="index")
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
     *     "_format": "xlsx|csv",
     *   }
     * )
     */
    public function run(Request $request, $_format)
    {
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

        /** @var Export $export */
        $export = new $exportClass($this->getDoctrine()->getManager());
        $form = $this->buildParameterForm($export);
        $form->submit($request->get($form->getName()));
        $parameters = $form->getData();

        $filename = preg_replace('/[^a-z0-9_]/i', '-', $export->getFilename($parameters));
        $type = Type::XLSX;
        switch ($_format) {
            case 'csv':
                $type = Type::CSV;
                break;
        }
        $filename .= '.'.$type;

        $writer = WriterFactory::createFromType($type);

        try {
            $writer->openToBrowser($filename);
            $export->write($parameters, $writer);
            $writer->close();
            exit;
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Error during export: '.$exception->getMessage());

            return $this->redirectToRoute('export_index');
        }
    }

    private function getFilename($parameters, $title, $format)
    {
        $title = preg_replace('/[^a-z0-9_-]/i', '-', $title);

        return $title.'.'.$format;
    }

    private function getExports()
    {
        $exports = [];

        foreach ($this->exportManager->getExports() as $export) {
            $exports[$this->getExportKey($export)] = $export;
        }

        return $exports;
    }

    private function getExportKey(AbstractExport $export)
    {
        return md5(\get_class($export));
    }

    private function buildParameterForm(AbstractExport $export)
    {
        $parameters = $export->getParameters();

        $builder = $this->get('form.factory')->createNamedBuilder('parameters_'.md5(\get_class($export)));
        foreach ($parameters as $name => $config) {
            $type = $config['type'] ?? null;
            $typeOptions = $config['type_options'] ?? [];
            $builder->add($name, $type, $typeOptions);
        }

        return $builder->getForm();
    }
}

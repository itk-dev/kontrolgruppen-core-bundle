<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Twig;

use Exception;
use Kontrolgruppen\CoreBundle\Entity\Conclusion;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private $conclusionService;
    private $translator;
    private $urlGenerator;

    /**
     * TwigExtension constructor.
     *
     * @param \Kontrolgruppen\CoreBundle\Service\ConclusionService $conclusionService
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ConclusionService $conclusionService,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->conclusionService = $conclusionService;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('yes_no', [$this, 'booleanYesNoFilter']),
            new TwigFilter('true_false', [$this, 'booleanTrueFalseFilter']),
            new TwigFilter('simple_date', [$this, 'simpleDateFilter'], ['needs_environment' => true]),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('iconClass', [$this, 'getIconClass']),
            new TwigFunction(
                'conclusionClassTranslation',
                [$this, 'getConclusionClassTranslation']
            ),
            new TwigFunction('enumTranslation', [$this, 'getEnumTranslation']),
            new TwigFunction('camelCaseToUnderscore', [$this, 'camelCaseToUnderscore']),
            new TwigFunction('urlToProcessRelatedClass', [$this, 'urlToProcessRelatedClass']),
        ];
    }

    public function simpleDateFilter(Environment $env, $date, $format = 'long')
    {
        $date = twig_date_converter($env, $date);

        switch ($format) {
            case 'short':
                return $date->format('d-m-Y');
                break;
            case 'long':
                return $date->format('d-m-Y H:i');
                break;
            default:
                return $this->simpleDateFilter($env, $date, 'long');
        }
    }

    public function booleanTrueFalseFilter($value)
    {
        if (null === $value) {
            return $this->translator->trans('common.boolean.null');
        }

        if ($value) {
            return $this->translator->trans('common.boolean.true');
        }

        return $this->translator->trans('common.boolean.false');
    }

    public function booleanYesNoFilter($value)
    {
        if (null === $value) {
            return $this->translator->trans('common.boolean.null');
        }

        if ($value) {
            return $this->translator->trans('common.boolean.yes');
        }

        return $this->translator->trans('common.boolean.no');
    }

    public function getEnumTranslation(string $value, $enum)
    {
        $className = 'Kontrolgruppen\\CoreBundle\\DBAL\\Types\\'.$enum;

        return $this->translator->trans(($className)::TRANSLATIONS[$value]);
    }

    public function getConclusionClassTranslation(string $className)
    {
        return $this->conclusionService->getTranslation($className);
    }

    public function getIconClass(string $name)
    {
        switch ($name) {
            case 'dashboard':
                return 'fa-tachometer-alt';
            case 'process':
                return 'fa-tasks';
            case 'profile':
                return 'fa-id-card';
            case 'users':
                return 'fa-users-cog';
            case 'admin':
                return 'fa-cog';
            case 'reminder':
                return 'fa-clock';
            case 'not-assigned':
                return 'fa-user-plus';
            case 'not-visited':
                return 'fa-archive';
            case 'show':
                return 'fa-eye';
            case 'hide':
                return 'fa-eye-slash';
            case 'edit':
                return 'fa-pencil-alt';
            case 'report':
                return 'fa-file-download';
            case 'print':
                return 'fa-print';
            case 'complete':
                return 'fa-check';
            case 'sort':
                return 'fa-sort';
            case 'sort-up':
                return 'fa-sort-up';
            case 'sort-down':
                return 'fa-sort-down';
            case 'layer-group':
                return 'fa-layer-group';
            case 'process-complete':
                return 'fa-door-closed';
            case 'process-resume':
                return 'fa-door-open';
            case 'user':
                return 'fa-user-circle';
            case 'save':
                return 'fa-save';
            case 'calendar':
                return 'fa-calendar';
            case 'error':
                return 'fa-times';
            case 'bi':
                return 'fa-chart-pie';
            case 'search-external':
                return 'fa-search';
            default:
                return '';
        }
    }

    public function camelCaseToUnderscore(string $camelCaseString)
    {
        $result = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCaseString));

        return $result;
    }

    /**
     * @TODO: Handle cases where there is not a show route for the given entity.
     */
    public function urlToProcessRelatedClass(string $class, int $id, int $processId)
    {
        try {
            $reflectedClass = new \ReflectionClass($class);

            if ($reflectedClass->isSubclassOf(Conclusion::class)) {
                return $this->urlGenerator->generate(
                    'conclusion_show',
                    [
                        'id' => $id,
                        'process' => $processId,
                    ]
                );
            } elseif (Process::class === $reflectedClass->getName()) {
                return $this->urlGenerator->generate(
                    'process_show',
                    [
                        'id' => $id,
                    ]
                );
            }

            $route = $this->camelCaseToUnderscore($reflectedClass->getShortName()).'_show';

            return $this->urlGenerator->generate(
                $route,
                [
                    'id' => $id,
                    'process' => $processId,
                ]
            );
        } catch (Exception $exception) {
            return '#';
        }
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TwigExtension extends AbstractExtension
{
    private $conclusionService;
    private $translator;
    private $doctrine;

    /**
     * TwigExtension constructor.
     *
     * @param $conclusionService
     * @param $doctrine
     */
    public function __construct(
        ConclusionService $conclusionService,
        TranslatorInterface $translator,
        RegistryInterface $doctrine
    ) {
        $this->conclusionService = $conclusionService;
        $this->translator = $translator;
        $this->doctrine = $doctrine;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('format_percent', [$this, 'getFormatPercent']),
            new TwigFilter('format_amount', [$this, 'getFormatAmount']),
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
        ];
    }

    /**
     * Formats a float value as an amount (money).
     *
     * @param $number
     * @return string
     */
    public function getFormatAmount($number)
    {
        return number_format($number, 2, ',', '.');
    }

    /**
     * Formats a float value as a percent string value, with an optional unit in the end.
     *
     * @param $number
     * @param bool $includeUnit
     * @return string
     */
    public function getFormatPercent($number, $includeUnit = true)
    {
        $number = number_format($number * 100.0, 2, ',', '.');
        $number = preg_replace("/\,?0+$/", "", $number);
        return $number.($includeUnit ? ' %' : '');
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
            default:
                return '';
        }
    }

    public function camelCaseToUnderscore(string $camelCaseString)
    {
        $result = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCaseString));

        return $result;
    }
}

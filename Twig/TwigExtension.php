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
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    private $conclusionService;
    private $translator;

    /**
     * TwigExtension constructor.
     *
     * @param $conclusionService
     */
    public function __construct(
        ConclusionService $conclusionService,
        TranslatorInterface $translator
    ) {
        $this->conclusionService = $conclusionService;
        $this->translator = $translator;
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
            new TwigFunction('monthYear', [$this, 'getMonthYearString']),
        ];
    }

    public function getMonthYearString(string $value = null)
    {
        if (null === $value) {
            return $value;
        }

        $parts = explode('/', $value);

        $dateObj = \DateTime::createFromFormat('!m', $parts[0]);

        if (!$dateObj) {
            return $value;
        }

        $monthName = $dateObj->format('M');

        return $monthName.'. '.$parts[1];
    }

    public function getEnumTranslation(string $value, $enum)
    {
        $className = 'Kontrolgruppen\\CoreBundle\\DBAL\\Types\\'.$enum;

        return $this->translator->trans(($className)::TRANSLATIONS[$value]);
    }

    public function getConclusionClassTranslation(string $className)
    {
        // @TODO: Replace with event.
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
}

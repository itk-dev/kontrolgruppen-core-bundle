<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\Entity\AbstractEntity;
use Kontrolgruppen\CoreBundle\Entity\BaseConclusion;
use Kontrolgruppen\CoreBundle\Entity\WeightedConclusion;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConclusionService.
 */
class ConclusionService
{
    private $translator;

    /**
     * ConclusionService constructor.
     *
     * @param $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getEntityFormType(AbstractEntity $entity)
    {
        return str_replace('Entity', 'Form', \get_class($entity).'Type');
    }

    public function getTemplate($class, $action = 'show')
    {
        switch ($class) {
            case BaseConclusion::class:
                return '@KontrolgruppenCore/conclusion/'.$action.'_base.html.twig';
            case WeightedConclusion::class:
                return '@KontrolgruppenCore/conclusion/'.$action.'_weighted.html.twig';
        }

        return false;
    }

    public function getTranslation($class)
    {
        switch ($class) {
            case BaseConclusion::class:
                return $this->translator->trans('conclusion.types.base');
            case WeightedConclusion::class:
                return $this->translator->trans('conclusion.types.weighted');
        }
    }

    public function getConclusionTypes()
    {
        $types = [];

        $types['conclusion.types.base'] = [
            'name' => $this->getTranslation(BaseConclusion::class),
            'class' => BaseConclusion::class,
        ];

        $types['conclusion.types.weighted'] = [
            'name' => $this->getTranslation(WeightedConclusion::class),
            'class' => WeightedConclusion::class,
        ];

        return $types;
    }
}

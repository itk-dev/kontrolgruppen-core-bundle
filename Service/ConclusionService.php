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
     * @param $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getEntityFormType(AbstractEntity $entity)
    {
        return str_replace('Entity', 'Form', get_class($entity)."Type");
    }

    public function getTemplate($class) {
        switch ($class) {
            case BaseConclusion::class:
                return '@KontrolgruppenCore/conclusion/show_base.html.twig';
            case WeightedConclusion::class:
                return '@KontrolgruppenCore/conclusion/show_weighted.html.twig';
        }

        return false;
    }

    public function getConclusionTypes()
    {
        $types = [];

        $types['conclusion.types.base'] = [
            'name' => $this->translator->trans('conclusion.types.base'),
            'class' => \Kontrolgruppen\CoreBundle\Entity\BaseConclusion::class,
        ];

        $types['conclusion.types.weighted'] = [
            'name' => $this->translator->trans('conclusion.types.weighted'),
            'class' => \Kontrolgruppen\CoreBundle\Entity\WeightedConclusion::class,
        ];

        return $types;
    }
}

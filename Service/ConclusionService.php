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

    /**
     * @param AbstractEntity $entity
     *
     * @return string|string[]
     */
    public function getEntityFormType(AbstractEntity $entity)
    {
        return str_replace('Entity', 'Form', \get_class($entity).'Type');
    }

    /**
     * @param        $class
     * @param string $action
     * @param string $basePath
     *
     * @return bool|string
     */
    public function getTemplate($class, $action = 'show', $basePath = '@KontrolgruppenCore/conclusion/')
    {
        // Making sure path has a trailing slash
        $basePath = rtrim($basePath, '/').'/';

        switch ($class) {
            case BaseConclusion::class:
                return $basePath.$action.'_base.html.twig';
            case WeightedConclusion::class:
                return $basePath.$action.'_weighted.html.twig';
        }

        return false;
    }

    /**
     * @param $class
     *
     * @return string
     */
    public function getTranslation($class)
    {
        switch ($class) {
            case BaseConclusion::class:
                return $this->translator->trans('conclusion.types.base');
            case WeightedConclusion::class:
                return $this->translator->trans('conclusion.types.weighted');
        }
    }

    /**
     * @return array
     */
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

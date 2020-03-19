<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\EventListener;

use Kontrolgruppen\CoreBundle\Entity\BaseConclusion;
use Kontrolgruppen\CoreBundle\Entity\WeightedConclusion;
use Kontrolgruppen\CoreBundle\Event\GetConclusionTemplateEvent;
use Kontrolgruppen\CoreBundle\Event\GetConclusionTypesEvent;
use Kontrolgruppen\CoreBundle\Service\ConclusionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ConclusionListener.
 */
class ConclusionListener implements EventSubscriberInterface
{
    private $conclusionService;

    /**
     * ConclusionListener constructor.
     *
     * @param ConclusionService $conclusionService
     */
    public function __construct(ConclusionService $conclusionService)
    {
        $this->conclusionService = $conclusionService;
    }

    /**
     * Registered events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            GetConclusionTypesEvent::NAME => 'onGetConclusionTypes',
            GetConclusionTemplateEvent::NAME => 'onGetConclusionTemplate',
        ];
    }

    /**
     * Supply conclusion types.
     *
     * @param GetConclusionTypesEvent $event
     */
    public function onGetConclusionTypes(GetConclusionTypesEvent $event)
    {
        $types = $event->getTypes();

        $types = array_merge($types, $this->conclusionService->getConclusionTypes());

        $event->setTypes($types);
    }

    /**
     * @param GetConclusionTemplateEvent $event
     */
    public function onGetConclusionTemplate(GetConclusionTemplateEvent $event)
    {
        // Only react to locally based conclusion templates.
        switch ($event->getClass()) {
            case WeightedConclusion::class:
            case BaseConclusion::class:
                $event->setTemplate($this->conclusionService->getTemplate($event->getClass(), $event->getAction()));
                break;
        }
    }
}

<?php

namespace Kontrolgruppen\CoreBundle\EventListener;

use Lexik\Bundle\FormFilterBundle\Event\GetFilterConditionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LikeFilterConditionListener implements EventSubscriberInterface
{
    /**
     * Registered events.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'lexik_form_filter.apply.orm.process_filter.caseNumber' => 'onGetFilterCondition',
            'lexik_form_filter.apply.orm.process_filter.clientCPR' => 'onGetFilterCondition',
        ];
    }

    public function onGetFilterCondition(GetFilterConditionEvent $event)
    {
        $expr   = $event->getFilterQuery()->getExpr();
        $values = $event->getValues();

        if (!empty($values['value'])) {
            // create a parameter name from the field
            $paramName = sprintf('p_%s', str_replace('.', '_', $event->getField()));

            // Set the condition on the given event
            $event->setCondition(

                $expr->like($event->getField(), ':' . $paramName),
                array($paramName => '%' .$values['value'] . '%')
            );
        }
    }
}

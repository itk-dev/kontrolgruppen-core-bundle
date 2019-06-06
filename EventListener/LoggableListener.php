<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\EventListener;

use Doctrine\Common\EventArgs;
use Gedmo\Loggable\LoggableListener as BaseLoggableListener;

class LoggableListener extends BaseLoggableListener
{
    private $actionLevelMapping = [
        'read'      => 'INFO',
        'default'   => 'INFO',
    ];

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array_merge(parent::getSubscribedEvents(), [

            'onRead',
        ]);
    }

    public function onRead(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();

        $object = $eventArgs->getProcess();

        $this->createLogEntry('read', $object, $ea);

        $om->flush();
    }

    /**
     * @inheritDoc
     */
    protected function prePersistLogEntry($logEntry, $object)
    {
        if (!method_exists($logEntry, 'setLevel')) {
            return;
        }

        $logEntry->setLevel(
            $this->getLevel($logEntry->getAction())
        );
    }

    private function getLevel(string $action): string
    {
        if (array_key_exists($action, $this->actionLevelMapping)) {

            return $this->actionLevelMapping[$action];
        }

        return $this->actionLevelMapping['default'];
    }
}

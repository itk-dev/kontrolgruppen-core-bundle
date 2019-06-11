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
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;

class LoggableListener extends BaseLoggableListener
{
    private $actionLevelMapping = [
        'read' => 'INFO',
        'default' => 'INFO',
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
     * {@inheritdoc}
     */
    protected function createLogEntry($action, $object, LoggableAdapter $ea)
    {
        $logEntry = parent::createLogEntry($action, $object, $ea);

        if (!empty($logEntry)) {

            if (in_array(get_class($object), [Process::class])) {

                $processLogEntry = new ProcessLogEntry();
                $processLogEntry->setLogEntry($logEntry);
                $processLogEntry->setProcess($object);
                $processLogEntry->setLevel('INFO');

                $objectManager = $ea->getObjectManager();
                $objectManager->persist($processLogEntry);
                $objectManager->flush();
            }
        }

        return $logEntry;
    }

    private function getLevel(string $action): string
    {
        if (\array_key_exists($action, $this->actionLevelMapping)) {
            return $this->actionLevelMapping[$action];
        }

        return $this->actionLevelMapping['default'];
    }
}

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
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\LoggableListener as BaseLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Entity\ProcessLoggableInterface;

class LoggableListener extends BaseLoggableListener
{
    private $actionLevelMapping = [
        'read' => ProcessLogEntryLevelEnumType::INFO,
        'create' => ProcessLogEntryLevelEnumType::NOTICE,
        'update' => ProcessLogEntryLevelEnumType::NOTICE,
        'remove' => ProcessLogEntryLevelEnumType::NOTICE,
        'default' => ProcessLogEntryLevelEnumType::INFO,
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
            if ($object instanceof Process) {
                $this->createProcessLogEntry(
                    $logEntry,
                    $object,
                    $this->getLevel($action),
                    $ea
                );
            //} else if (method_exists($object, 'getProcess')) {
            } elseif ($object instanceof ProcessLoggableInterface) {
                $this->createProcessLogEntry(
                    $logEntry,
                    $object->getProcess(),
                    $this->getLevel($action),
                    $ea
                );
            }
        }

        return $logEntry;
    }

    private function createProcessLogEntry(LogEntry $logEntry, Process $process, string $level, LoggableAdapter $ea)
    {
        $processLogEntry = new ProcessLogEntry();
        $processLogEntry->setLogEntry($logEntry);
        $processLogEntry->setProcess($process);
        $processLogEntry->setLevel($level);

        $objectManager = $ea->getObjectManager();
        $objectManager->persist($processLogEntry);

        $uow = $objectManager->getUnitOfWork();
        $uow->computeChangeSet(
            $objectManager->getClassMetadata(\get_class($processLogEntry)),
            $processLogEntry
        );
    }

    private function getLevel(string $action): string
    {
        if (\array_key_exists($action, $this->actionLevelMapping)) {
            return $this->actionLevelMapping[$action];
        }

        return $this->actionLevelMapping['default'];
    }
}

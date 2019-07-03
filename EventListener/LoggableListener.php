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

    protected function prePersistLogEntry($logEntry, $object)
    {
        $data = $logEntry->getData();

        // If data is iterable it is probably describing a relationship, and we need to add some data. If it is not iterable there is no need.
        if (!is_iterable($data)) {
            return;
        }

        $newData = [];

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'channel':
                    $newData[$key]['name'] = null !== $object->getChannel()
                        ? $object->getChannel()->getName()
                        : null;
                    break;

                case 'service':
                    $newData[$key]['name'] = null !== $object->getService()
                        ? $object->getService()->getName()
                        : null;
                    break;

                case 'processType':
                    $newData[$key]['name'] = null !== $object->getProcessType()
                        ? $object->getProcessType()->getName()
                        : null;
                    break;

                case 'processStatus':
                    $newData[$key]['name'] = null !== $object->getProcessStatus()
                        ? $object->getProcessStatus()->getName()
                        : null;
                    break;
                case 'reason':
                    $newData[$key]['name'] = null !== $object->getReason()
                        ? $object->getReason()->getName()
                        : null;
                    break;

                default:
                    break;
            }
        }

        $data = array_merge_recursive($data, $newData);

        $logEntry->setData($data);
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

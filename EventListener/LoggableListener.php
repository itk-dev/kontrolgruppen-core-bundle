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
use Kontrolgruppen\CoreBundle\Entity\Conclusion;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessGroup;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Entity\ProcessLoggableInterface;
use Kontrolgruppen\CoreBundle\Entity\User;

/**
 * Class LoggableListener.
 */
class LoggableListener extends BaseLoggableListener
{
    private $creatorName;

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

    /**
     * @param EventArgs $eventArgs
     */
    public function onRead(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();

        $object = $eventArgs->getProcess();

        $this->createLogEntry('read', $object, $ea);

        $om->flush();
    }

    /**
     * Set creator name.
     *
     * @param $creatorName
     */
    public function setCreatorName($creatorName)
    {
        if (\is_string($creatorName)) {
            $this->creatorName = $creatorName;
        } elseif (\is_object($creatorName) && method_exists($creatorName, 'getUser')) {
            /** @var User $user */
            $user = $creatorName->getUser();
            $this->creatorName = $user->getName();
        }
    }

    /**
     * @param object $logEntry
     * @param object $object
     */
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
                case 'caseWorker':
                    $newData[$key]['name'] = null !== $object->getCaseWorker()
                        ? $object->getCaseWorker()->getUsername()
                        : null;
                    break;
                case 'primaryProcess':
                    $newData[$key]['caseNumber'] = null !== $object->getPrimaryProcess()
                        ? $object->getPrimaryProcess()->getCaseNumber()
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
        // We dont want to log creation of Conclusions, as they only clutter the logs.
        if (parent::ACTION_CREATE === $action && is_subclass_of($object, Conclusion::class)) {
            return;
        }

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
            } elseif ($object instanceof ProcessGroup) {
                foreach ($object->getProcesses() as $process) {
                    $this->createProcessLogEntry(
                        $logEntry,
                        $process,
                        $this->getLevel($action),
                        $ea
                    );
                }
            }
        }

        return $logEntry;
    }

    /**
     * @param LogEntry        $logEntry
     * @param Process         $process
     * @param string          $level
     * @param LoggableAdapter $ea
     */
    private function createProcessLogEntry(LogEntry $logEntry, Process $process, string $level, LoggableAdapter $ea)
    {
        $processLogEntry = new ProcessLogEntry();
        $processLogEntry->setLogEntry($logEntry);
        $processLogEntry->setProcess($process);
        $processLogEntry->setLevel($level);
        $processLogEntry->setCreatorName($this->creatorName);

        $objectManager = $ea->getObjectManager();
        $objectManager->persist($processLogEntry);

        $uow = $objectManager->getUnitOfWork();
        $uow->computeChangeSet(
            $objectManager->getClassMetadata(\get_class($processLogEntry)),
            $processLogEntry
        );
    }

    /**
     * @param string $action
     *
     * @return string
     */
    private function getLevel(string $action): string
    {
        if (\array_key_exists($action, $this->actionLevelMapping)) {
            return $this->actionLevelMapping[$action];
        }

        return $this->actionLevelMapping['default'];
    }
}

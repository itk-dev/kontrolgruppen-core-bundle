<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;

class LogManager
{
    protected $entityManager;

    /**
     * LogManager constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function attachLogEntriesToJournalEntries($journalEntries)
    {
        $journalEntryIds = array_reduce(
            $journalEntries,
            function ($carry, $item) {
                $carry[] = $item['id'];
                return $carry;
            }, []
        );
        $qb = $this->entityManager->createQueryBuilder('log');
        $qb->select('log')->from(LogEntry::class, 'log')
            ->where('log.objectId IN (:journalEntryIds)')
            ->andWhere('log.objectClass = \''.JournalEntry::class.'\'');
        $qb->setParameter('journalEntryIds', $journalEntryIds);
        $logs = $qb->getQuery()->execute();
        foreach ($logs as $log) {
            if (!isset($journalEntries[$log->getObjectId()]['logs'])) {
                $journalEntries[$log->getObjectId()]['logs'] = [];
            }
            $journalEntries[$log->getObjectId()]['logs'][] = $log;
        }

        return $journalEntries;
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Service\LogManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method JournalEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method JournalEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method JournalEntry[]    findAll()
 * @method JournalEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalEntryRepository extends ServiceEntityRepository
{
    protected $logManager;

    public function __construct(RegistryInterface $registry, LogManager $logManager)
    {
        $this->logManager = $logManager;

        parent::__construct($registry, JournalEntry::class);
    }

    public function getLatestNoteEntries(Process $process)
    {
        return $this->getLatestEntries($process, JournalEntryEnumType::NOTE);
    }

    public function getLatestInternalNoteEntries(Process $process)
    {
        return $this->getLatestEntries($process, JournalEntryEnumType::INTERNAL_NOTE);
    }

    /**
     * Get latest journal entries for a given process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process          the process the journal entries apply to
     * @param string|null                               $type             type of entry
     * @param int|null                                  $limit            limit on number of results
     * @param bool                                      $attachLogEntries attach the log entries for journal entries
     *
     * @return array
     */
    public function getLatestEntries(Process $process, string $type = null, int $limit = null, bool $attachLogEntries = false)
    {
        $qb = $this->createQueryBuilder('journalEntry', 'journalEntry.id');
        $qb
            ->where('journalEntry.process = :process')
            ->setParameter('process', $process)
            ->orderBy('journalEntry.createdAt', 'DESC')
        ;

        if (null !== $type) {
            $qb->andWhere('journalEntry.type = :type')
                ->setParameter('type', $type);
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        $result = $qb->getQuery()->getArrayResult();

        if ($attachLogEntries) {
            $result = $this->logManager->attachLogEntriesToJournalEntries($result);
        }

        return $result;
    }
}

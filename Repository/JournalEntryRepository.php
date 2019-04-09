<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType;

/**
 * @method JournalEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method JournalEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method JournalEntry[]    findAll()
 * @method JournalEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalEntryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, JournalEntry::class);
    }

    public function getLatestDiaryEntries(Process $process)
    {
        return $this->getLatestEntries($process, JournalEntryEnumType::DIARY);
    }

    public function getLatestNoteEntries(Process $process)
    {
        return $this->getLatestEntries($process, JournalEntryEnumType::NOTE);
    }

    public function getLatestInternalNoteEntries(Process $process)
    {
        return $this->getLatestEntries($process, JournalEntryEnumType::INTERNAL_NOTE);
    }

    public function getLatestEntries(Process $process, string $type, int $limit = null)
    {
        $qb = $this->createQueryBuilder('journalEntry');
        $qb
            ->where('journalEntry.process = :process')
            ->setParameter('process', $process)
            ->andWhere('journalEntry.type = :type')
            ->setParameter('type', $type)
            ->orderBy('journalEntry.updatedAt', 'DESC')
        ;

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}

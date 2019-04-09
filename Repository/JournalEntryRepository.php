<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
}

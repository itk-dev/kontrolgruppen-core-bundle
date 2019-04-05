<?php

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Kontrolgruppen\CoreBundle\Entity\User;

/**
 * @method Reminder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reminder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reminder[]    findAll()
 * @method Reminder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReminderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reminder::class);
    }

    public function findAllUserReminders(User $user)
    {
        $qb = $this->createQueryBuilder('reminder')
            ->leftJoin('reminder.process', 'process')->addSelect('process')
            ->where('process.caseWorker = :user')
            ->setParameter('user', $user)
            ->getQuery();

        return $qb->execute();
    }

    public function findActiveUserReminders(User $user)
    {
        $now = new \DateTime();

        $qb = $this->createQueryBuilder('reminder')
            ->where(':user = process.caseWorker')
            ->andWhere('reminder.date < :now')
            ->andWhere('reminder.finished != true')
            ->leftJoin('reminder.process', 'process')->addSelect("process")
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->getQuery();

        return $qb->execute();
    }
}

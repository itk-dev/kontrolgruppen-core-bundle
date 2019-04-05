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
        $qb = $this->createQueryBuilder('reminder');
        $expr = $qb->expr();
        $qb = $qb
            ->select('reminder', 'process')
            ->leftJoin('reminder.process', 'process')->addSelect("process")
            ->where('process.caseWorker = :user')
            ->setParameter('user', $user)
            ->andWhere($expr->orX(
                $expr->isNull('reminder.finished'),
                $expr->neq('reminder.finished', true))
            )
            ->andWhere('reminder.date < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery();

        return $qb->execute();
    }
}

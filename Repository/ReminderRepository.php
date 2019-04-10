<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\DBAL\Types\DateIntervalType;
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

    /**
     * Find all reminders for user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *
     * @return mixed
     */
    public function findAllUserReminders(User $user)
    {
        $qb = $this->createQueryBuilder('reminder')
            ->leftJoin('reminder.process', 'process')->addSelect('process')
            ->where('process.caseWorker = :user')
            ->setParameter('user', $user)
            ->getQuery();

        return $qb->execute();
    }

    /**
     * Find active reminders for user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *
     * @return mixed
     */
    public function findActiveUserReminders(User $user)
    {
        $qb = $this->createQueryBuilder('reminder');
        $expr = $qb->expr();
        $qb = $qb
            ->select('reminder')
            ->leftJoin('reminder.process', 'process')->addSelect('process')
            ->where('process.caseWorker = :user')
            ->setParameter('user', $user)
            ->andWhere(
                $expr->orX(
                    $expr->isNull('reminder.finished'),
                    $expr->neq('reminder.finished', true)
                )
            )
            ->andWhere('reminder.date < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery();

        return $qb->execute();
    }

    /**
     * Count the number of active reminders for user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findNumberOfActiveUserReminders(User $user)
    {
        $qb = $this->createQueryBuilder('reminder');
        $expr = $qb->expr();
        $qb = $qb
            ->select('count(reminder) as c')
            ->leftJoin('reminder.process', 'process')
            ->where('process.caseWorker = :user')
            ->setParameter('user', $user)
            ->andWhere(
                $expr->orX(
                    $expr->isNull('reminder.finished'),
                    $expr->neq('reminder.finished', true)
                )
            )
            ->andWhere('reminder.date < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery();

        return $qb->getSingleScalarResult();
    }

    /**
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     * @param string $interval From DateIntervalType.
     * @return mixed
     * @throws \Exception
     */
    public function findComingUserReminders(User $user, string $interval = null) {
        $now = new \DateTime();

        $qb = $this->createQueryBuilder('reminder');
        $expr = $qb->expr();
        $qb = $qb
            ->select('reminder')
            ->leftJoin('reminder.process', 'process')->addSelect('process')
            ->where('process.caseWorker = :user')
            ->setParameter('user', $user)
            ->andWhere(
                $expr->orX(
                    $expr->isNull('reminder.finished'),
                    $expr->neq('reminder.finished', true)
                )
            )
            ->andWhere('reminder.date > :now')
            ->setParameter('now', $now);

        if ($interval == DateIntervalType::THIS_WEEK) {
            $qb->andWhere('WEEK(CURRENT_DATE()) = WEEK(reminder.date)');
        }
        else if ($interval == DateIntervalType::WEEK) {
            $qb->andWhere('reminder.date BETWEEN :now AND :to')
                ->setParameter('to', (new \DateTime())->add(new \DateInterval("P7D")));

            $qb->andWhere('MONTH(CURRENT_DATE()) = MONTH(reminder.date)');
        }
        else if ($interval == DateIntervalType::TWO_WEEKS) {
            $qb->andWhere('reminder.date BETWEEN :now AND :to')
                ->setParameter('to', (new \DateTime())->add(new \DateInterval("P14D")));

            $qb->andWhere('MONTH(CURRENT_DATE()) = MONTH(reminder.date)');
        }
        else if ($interval == DateIntervalType::MONTH) {
            $qb->andWhere('reminder.date BETWEEN :now AND :to')
                ->setParameter('to', (new \DateTime())->add(new \DateInterval("P1M")));

            $qb->andWhere('MONTH(CURRENT_DATE()) = MONTH(reminder.date)');
        }

        return $qb->getQuery()->execute();
    }
}

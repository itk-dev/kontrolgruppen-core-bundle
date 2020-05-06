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
use Doctrine\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\DBAL\Types\DateIntervalType;
use Kontrolgruppen\CoreBundle\Entity\Reminder;
use Kontrolgruppen\CoreBundle\Entity\User;

/**
 * @method Reminder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reminder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reminder[]    findAll()
 * @method Reminder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReminderRepository extends ServiceEntityRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reminder::class);
    }

    /**
     * Find all reminders for user.
     *
     * @param User $user
     *
     * @return mixed
     *   The reminders
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
     * @param User $user
     *
     * @return mixed
     *   The active reminders
     *
     * @throws \Exception
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
     * @param User $user
     *
     * @return mixed
     *   Number of active reminders
     *
     * @throws \Doctrine\ORM\NoResultException
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
     * Get future user reminders.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *   The user
     * @param string                                 $interval
     *   The interval (from DateIntervalType)
     * @param bool                                   $sortByDate
     *   Sort by date
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function findComingUserReminders(User $user, string $interval = null, $sortByDate = false)
    {
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

        if (DateIntervalType::THIS_WEEK === $interval) {
            $qb->andWhere('WEEK(CURRENT_DATE()) = WEEK(reminder.date)');
        } elseif (DateIntervalType::WEEK === $interval) {
            $qb->andWhere('reminder.date BETWEEN :now AND :to')
                ->setParameter('to', (new \DateTime())->add(new \DateInterval('P7D')));

            $qb->andWhere('MONTH(CURRENT_DATE()) = MONTH(reminder.date)');
        } elseif (DateIntervalType::TWO_WEEKS === $interval) {
            $qb->andWhere('reminder.date BETWEEN :now AND :to')
                ->setParameter('to', (new \DateTime())->add(new \DateInterval('P14D')));

            $qb->andWhere('MONTH(CURRENT_DATE()) = MONTH(reminder.date)');
        } elseif (DateIntervalType::MONTH === $interval) {
            $qb->andWhere('reminder.date BETWEEN :now AND :to')
                ->setParameter('to', (new \DateTime())->add(new \DateInterval('P1M')));

            $qb->andWhere('MONTH(CURRENT_DATE()) = MONTH(reminder.date)');
        }

        if ($sortByDate) {
            $results = $qb->getQuery()->execute();

            $dateResults = [];

            foreach ($results as $result) {
                $day = $result->getDate()->format('Y-m-d');
                $day = (new \DateTime($day))->format('c');

                if (!isset($dateResults[$day])) {
                    $dateResults[$day] = [];
                }

                $dateResults[$day][] = $result;
            }

            return $dateResults;
        }

        return $qb->getQuery()->execute();
    }
}

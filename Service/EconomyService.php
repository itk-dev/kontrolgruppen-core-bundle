<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Kontrolgruppen\CoreBundle\Repository\LockedNetValueRepository;

/**
 * Class EconomyService.
 */
class EconomyService
{
    private $economyEntryRepository;
    private $lockedNetValueRepository;

    /**
     * EconomyService constructor.
     */
    public function __construct(EconomyEntryRepository $economyEntryRepository, LockedNetValueRepository $lockedNetValueRepository)
    {
        $this->economyEntryRepository = $economyEntryRepository;
        $this->lockedNetValueRepository = $lockedNetValueRepository;
    }

    /**
     * Calculate the revenue for a given process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *
     * @return array
     */
    public function calculateRevenue(Process $process)
    {
        $serviceEconomyEntries = $this->economyEntryRepository->findBy([
            'process' => $process,
            'type' => EconomyEntryEnumType::SERVICE,
        ]);

        $result = array_reduce($serviceEconomyEntries, function ($carry, ServiceEconomyEntry $entry) use ($process) {
            if (null !== $entry->getRepaymentAmount()) {
                $carry['entries'][] = $entry;
                $carry['repaymentSum'] = $carry['repaymentSum'] + $entry->getRepaymentAmount();

                $lockedNetValue = $this->lockedNetValueRepository->findBy(['process' => $process, 'service' => $entry->getService()]);

                $netMultiplier = (!empty($lockedNetValue))
                                    ? $lockedNetValue[0]->getValue()
                                    : $entry->getService()->getNetDefaultValue();

                $carry['netRepaymentSum'] = $carry['netRepaymentSum'] + ($entry->getRepaymentAmount() * $netMultiplier) * $this->getMonthsBetweenDates($entry->getRepaymentPeriodFrom(), $entry->getRepaymentPeriodTo());

                if (!isset($carry['repaymentSums'][$entry->getService()->getName()])) {
                    $carry['repaymentSums'][$entry->getService()->getName()] = 0;
                }
                $carry['repaymentSums'][$entry->getService()->getName()] = [
                    'netMultiplier' => $netMultiplier * 100,
                    'sum' => $carry['repaymentSums'][$entry->getService()->getName()]['sum'] + $entry->getRepaymentAmount(),
                ];
            }

            if (null !== $entry->getFutureSavingsAmount()) {
                // Calculate yearly future savings
                $carry['futureSavingsSum'] = $carry['futureSavingsSum'] + $entry->getFutureSavingsAmount();
                $carry['netFutureSavingsSum'] = $carry['netFutureSavingsSum'] + ($entry->getFutureSavingsAmount() * $netMultiplier) * $this->getMonthsBetweenDates($entry->getFutureSavingsPeriodFrom(), $entry->getFutureSavingsPeriodTo());

                if (!isset($carry['futureSavingsSums'][$entry->getService()->getName()])) {
                    $carry['futureSavingsSums'][$entry->getService()->getName()] = 0;
                }

                $carry['futureSavingsSums'][$entry->getService()->getName()] = [
                    'netMultiplier' => $netMultiplier * 100,
                    'sum' => $carry['futureSavingsSums'][$entry->getService()->getName()]['sum'] + $entry->getFutureSavingsAmount(),
                ];
            }

            return $carry;
        }, [
            // Entries where the repayment amount has been set.
            'entries' => [],
            // Sum of all repayment amounts.
            'repaymentSum' => 0.0,
            // Repayment amount sums from each Service.
            'repaymentSums' => [],
            // Net repayment amount sum.
            'netRepaymentSum' => 0.0,
            // Sum of future savings.
            // That is all service entries that have a repayment amount set, are assumed to be zero in the future.
            'futureSavingsSum' => 0.0,
            // Future savings sums for each Service.
            'futureSavingsSums' => [],
            // Net future savings sum.
            'netFutureSavingsSum' => 0.0,
        ]);

        $result['netCollectiveSum'] = $result['netRepaymentSum'] + $result['netFutureSavingsSum'];
        $result['collectiveSum'] = $result['repaymentSum'] + $result['futureSavingsSum'];

        return $result;
    }

    private function getMonthsBetweenDates(\DateTime $from, \DateTime $to)
    {
        $interval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($from, $interval, $to);

        return iterator_count($period);
    }
}

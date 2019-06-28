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

/**
 * Class EconomyService.
 */
class EconomyService
{
    private $economyEntryRepository;

    /**
     * EconomyService constructor.
     */
    public function __construct(EconomyEntryRepository $economyEntryRepository)
    {
        $this->economyEntryRepository = $economyEntryRepository;
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
        $netMultiplier = $process->getProcessType()->getNetDefaultValue();

        $serviceEconomyEntries = $this->economyEntryRepository->findBy([
            'process' => $process,
            'type' => EconomyEntryEnumType::SERVICE,
        ]);

        $result = array_reduce($serviceEconomyEntries, function ($carry, ServiceEconomyEntry $entry) use ($netMultiplier) {
            if (null !== $entry->getRepaymentAmount()) {
                $carry['entries'][] = $entry;
                $carry['repaymentSum'] = $carry['repaymentSum'] + $entry->getRepaymentAmount();
                $carry['netRepaymentSum'] = $carry['netRepaymentSum'] + ($entry->getRepaymentAmount() * $netMultiplier);

                if (!isset($carry['repaymentSums'][$entry->getService()->getName()])) {
                    $carry['repaymentSums'][$entry->getService()->getName()] = 0;
                }
                $carry['repaymentSums'][$entry->getService()->getName()] = $carry['repaymentSums'][$entry->getService()->getName()] + $entry->getRepaymentAmount();

                // Calcalute yearly future savings.
                $futureSavings = $entry->getAmount() / $entry->getAmountPeriod() * 12.0;

                $carry['futureSavingsSum'] = $carry['futureSavingsSum'] + $futureSavings;

                if (!isset($carry['futureSavingsSums'][$entry->getService()->getName()])) {
                    $carry['futureSavingsSums'][$entry->getService()->getName()] = 0;
                }
                $carry['futureSavingsSums'][$entry->getService()->getName()] = $carry['futureSavingsSums'][$entry->getService()->getName()] + $futureSavings;
                $carry['netFutureSavingsSum'] = $carry['netFutureSavingsSum'] + $futureSavings * $netMultiplier;
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

        $result['collectiveNetSum'] = $result['netRepaymentSum'] + $result['netFutureSavingsSum'];
        $result['netMultiplier'] = $netMultiplier;

        return $result;
    }
}

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
use Kontrolgruppen\CoreBundle\DBAL\Types\RevenueFutureTypeEnumType;
use Kontrolgruppen\CoreBundle\DBAL\Types\RevenueTypeEnumType;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\RevenueEntry;
use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Kontrolgruppen\CoreBundle\Repository\LockedNetValueRepository;
use Kontrolgruppen\CoreBundle\Repository\RevenueEntryRepository;

/**
 * Class EconomyService.
 */
class EconomyService
{
    private $revenueEntryRepository;
    private $lockedNetValueRepository;
    private $economyEntryRepository;

    /**
     * EconomyService constructor.
     *
     * @param RevenueEntryRepository   $revenueEntryRepository
     * @param EconomyEntryRepository   $economyEntryRepository
     * @param LockedNetValueRepository $lockedNetValueRepository
     */
    public function __construct(RevenueEntryRepository $revenueEntryRepository, EconomyEntryRepository $economyEntryRepository, LockedNetValueRepository $lockedNetValueRepository)
    {
        $this->revenueEntryRepository = $revenueEntryRepository;
        $this->lockedNetValueRepository = $lockedNetValueRepository;
        $this->economyEntryRepository = $economyEntryRepository;
    }

    /**
     * Calculate the revenue for a given process.
     *
     * @return array
     */
    public function calculateRevenue(Process $process)
    {
        $revenueEntries = $this->revenueEntryRepository->findBy([
            'process' => $process,
        ]);

        $serviceEconomyEntries = $this->economyEntryRepository->findBy([
            'process' => $process,
            'type' => EconomyEntryEnumType::SERVICE,
        ]);

        $services = array_reduce($serviceEconomyEntries, function ($carry, ServiceEconomyEntry $entry) {
            $carry[$entry->getService()->getId()] = $entry->getService();
            return $carry;
        }, []);

        $result = array_reduce($revenueEntries, function ($carry, RevenueEntry $entry) use ($process, $services) {
            $lockedNetValue = $this->lockedNetValueRepository->findOneBy([
                'process' => $process,
                'service' => $entry->getService(),
            ]);

            $netMultiplier = null !== $lockedNetValue
                ? $lockedNetValue->getValue()
                : ($entry->getService()->getNetDefaultValue() ?? 1.0);

            // Ignore entry if the service is not in service economy entries.
            if (!isset($services[$entry->getService()->getId()])) {
                return $carry;
            }

            if (null !== $entry->getAmount()) {
                $amount = $entry->getAmount();
                $serviceName = $entry->getService()->getName();
                $carry['entries'][] = $entry;

                if ($entry->getType() === RevenueTypeEnumType::REPAYMENT) {
                    $carry['repaymentSum'] = $carry['repaymentSum'] + $amount;
                    $netAmount = $amount * $netMultiplier;
                    $carry['netRepaymentSum'] = $carry['netRepaymentSum'] + $netAmount;

                    $carry['repaymentSums'][$serviceName] = [
                        'netPercentage' => $netMultiplier * 100,
                        'sum' => $carry['repaymentSums'][$serviceName]['sum'] ?? 0 + $amount,
                    ];
                }
                else if ($entry->getType() === RevenueTypeEnumType::FUTURE_SAVINGS) {
                    $calculatedAmount = $amount * ($entry->getFutureSavingsType() === RevenueFutureTypeEnumType::PR_MND_X_12 ? 12.0 : 1.0);
                    $carry['futureSavingsSum'] = $carry['futureSavingsSum'] + $calculatedAmount;
                    $carry['netFutureSavingsSum'] = $carry['netFutureSavingsSum'] + ($calculatedAmount * $netMultiplier);

                    $carry['futureSavingsSums'][$serviceName] = [
                        'netPercentage' => $netMultiplier * 100,
                        'sum' => $carry['futureSavingsSums'][$serviceName]['sum'] ?? 0 + $calculatedAmount,
                    ];
                }
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
}

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
     * @param Process $process
     *   The process to calculate revenue for
     *
     * @return array
     *   Array of calculated revenue values. See data structure in $defaultResult.
     */
    public function calculateRevenue(Process $process)
    {
        $defaultResult = [
            // All entries that are included in the calculation.
            'entries' => [],
            // Sum of repayment.
            'repaymentSum' => 0.0,
            // Repayment amount sums from each Service. Indexed by service name.
            'repaymentSums' => [],
            // Net repayment amount sum.
            'netRepaymentSum' => 0.0,
            // Sum of future savings.
            'futureSavingsSum' => 0.0,
            // Future savings sums for each Service. Indexed by service name.
            'futureSavingsSums' => [],
            // Net future savings sum.
            'netFutureSavingsSum' => 0.0,
            // Sum of net future savings sum and net repayment sum.
            'netCollectiveSum' => 0.0,
            // Sum of future savings sum and repayment sum.
            'collectiveSum' => 0.0,
        ];

        $revenueEntries = $this->revenueEntryRepository->findBy([
            'process' => $process,
        ]);

        $serviceEconomyEntries = $this->economyEntryRepository->findBy([
            'process' => $process,
            'type' => EconomyEntryEnumType::SERVICE,
        ]);

        $services = array_reduce($serviceEconomyEntries, function ($carry, ServiceEconomyEntry $entry) {
            if (null !== $entry->getService()) {
                $carry[$entry->getService()->getId()] = $entry->getService();
            }

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

                if (RevenueTypeEnumType::REPAYMENT === $entry->getType()) {
                    $carry['repaymentSum'] = $carry['repaymentSum'] + $amount;
                    $netAmount = $amount * $netMultiplier;
                    $carry['netRepaymentSum'] = $carry['netRepaymentSum'] + $netAmount;

                    $carry['repaymentSums'][$serviceName] = [
                        'netPercentage' => $netMultiplier * 100,
                        'sum' => ($carry['repaymentSums'][$serviceName]['sum'] ?? 0.0) + $amount,
                    ];
                } elseif (RevenueTypeEnumType::FUTURE_SAVINGS === $entry->getType()) {
                    $futureSavingsMultiplier = 1.0;

                    if (RevenueFutureTypeEnumType::PR_MND_X_12 === $entry->getFutureSavingsType()) {
                        $futureSavingsMultiplier = 12.0;
                    } elseif (RevenueFutureTypeEnumType::PR_WEEK_X_52 === $entry->getFutureSavingsType()) {
                        $futureSavingsMultiplier = 52.0;
                    }

                    $calculatedAmount = $amount * $futureSavingsMultiplier;

                    $carry['futureSavingsSum'] = $carry['futureSavingsSum'] + $calculatedAmount;
                    $carry['netFutureSavingsSum'] = $carry['netFutureSavingsSum'] + ($calculatedAmount * $netMultiplier);

                    $carry['futureSavingsSums'][$serviceName] = [
                        'netPercentage' => $netMultiplier * 100,
                        'sum' => ($carry['futureSavingsSums'][$serviceName]['sum'] ?? 0.0) + $calculatedAmount,
                    ];
                }
            }

            return $carry;
        }, $defaultResult);

        $result['netCollectiveSum'] = $result['netRepaymentSum'] + $result['netFutureSavingsSum'];
        $result['collectiveSum'] = $result['repaymentSum'] + $result['futureSavingsSum'];

        return $result;
    }
}

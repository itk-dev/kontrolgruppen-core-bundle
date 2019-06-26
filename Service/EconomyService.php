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

    public function calculateRevenue(Process $process)
    {
        $serviceEconomyEntries = $this->economyEntryRepository->findBy([
            'process' => $process,
            'type' => EconomyEntryEnumType::SERVICE,
        ]);

        // @TODO: Group by service.

        $result = array_reduce($serviceEconomyEntries, function ($carry, ServiceEconomyEntry $entry) {
            if ($entry->getRepaymentAmount() != null) {
                $carry['entries'][] = $entry;
                $carry['repaymentSum'] = $carry['sum'] + $entry->getRepaymentAmount();
                // @TODO: Configurable.
                $carry['nettoRepaymentSum'] = $carry['netto'] + ($entry->getRepaymentAmount() * .7);

                if (!isset($carry['repaymentSums'][$entry->getService()->getName()])) {
                    $carry['repaymentSums'][$entry->getService()->getName()] = 0;
                }
                $carry['repaymentSums'][$entry->getService()->getName()] = $carry['repaymentSums'][$entry->getService()->getName()] + $entry->getRepaymentAmount();

                $futureSavings = $entry->getAmount() / $entry->getAmountPeriod() * 12.0;

                $carry['futureSavingsSum'] = $carry['futureSavingsSum'] + $futureSavings;

                if (!isset($carry['futureSavingsSums'][$entry->getService()->getName()])) {
                    $carry['futureSavingsSums'][$entry->getService()->getName()] = 0;
                }
                $carry['futureSavingsSums'][$entry->getService()->getName()] = $carry['futureSavingsSums'][$entry->getService()->getName()] + $futureSavings;

                // @TODO: Configurable.
                $carry['nettoFutureSavingsSum'] = $carry['nettoFutureSavingsSum'] + $futureSavings * .7;
            }

            return $carry;
        }, [
            'entries' => [],
            'repaymentSum' => 0.0,
            'repaymentSums' => [],
            'nettoRepaymentSum' => 0.0,
            'futureSavingsSum' => 0.0,
            'futureSavingsSums' => [],
            'nettoFutureSavingsSum' => 0.0,
        ]);

        return $result;
    }
}

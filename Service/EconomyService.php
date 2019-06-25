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
        $result = [
            'repaymentEntries' => [],
            'collectiveFutureSavings' => [],
        ];

        $serviceEconomyEntries = $this->economyEntryRepository->findBy([
            'process' => $process,
            'type' => EconomyEntryEnumType::SERVICE,
        ]);

        $result['repayment'] = array_reduce($serviceEconomyEntries, function ($carry, $entry) {
            if ($entry->getRepaymentAmount() != null) {
                $carry['entries'][] = $entry;
                $carry['sum'] = $carry['sum'] + $entry->getRepaymentAmount();
                // @TODO: Configurable.
                $carry['netto'] = $carry['netto'] + ($entry->getRepaymentAmount() * .7);
            }

            return $carry;
        }, [
            'entries' => [],
            'sum' => 0.0,
            'netto' => 0.0,
        ]);

        return $result;
    }
}

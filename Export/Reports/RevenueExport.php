<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\Reports;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Service\EconomyService;

class RevenueExport extends AbstractExport
{
    protected $title = 'Samlet provenue opdelt på ydelser';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, EconomyService $economyService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->economyService = $economyService;
    }

    public function getParameters()
    {
        return parent::getParameters();
    }

    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->writeHeader([
            'Ydelse',
            'Antal afsluttede sager',
            'Samlet tilbagebetalingskrav (Brutto)',
            'Samlet fremadrettet besparelse (Brutto)',
            'Samlet provenu (Brutto)',
            'Provenu pr. sag (Brutto)',
            'Samlet tilbagebetalingskrav (Netto)',
            'Samlet fremadrettet besparelse (Netto)',
            'Samlet provenu (Netto)',
            'Provenu pr. sag (Netto)',
        ]);

        $revenue = [];

        foreach ($processes as $process) {
            $countedForService = [];
            $processRevenue = $this->economyService->calculateRevenue($process);

            foreach ($processRevenue['repaymentSums'] as $serviceName => $repaymentSum) {
                if (!isset($revenue[$serviceName])) {
                    $revenue[$serviceName] = $this->newEntry();
                }

                if (!isset($countedForService[$serviceName])) {
                    ++$revenue[$serviceName]['processes'];
                    $countedForService[$serviceName] = $serviceName;
                }
                $revenue[$serviceName]['collectiveRepaymentSum'] += $repaymentSum['sum'];
                $revenue[$serviceName]['collectiveRevenueSum'] += $repaymentSum['sum'];
                $revenue[$serviceName]['netCollectiveRepaymentSum'] += $repaymentSum['sum'] * $repaymentSum['netPercentage'] / 100.0;
                $revenue[$serviceName]['netCollectiveRevenueSum'] += $repaymentSum['sum'] * $repaymentSum['netPercentage'] / 100.0;
            }

            foreach ($processRevenue['futureSavingsSums'] as $serviceName => $futureSavingsSum) {
                if (!isset($revenue[$serviceName])) {
                    $revenue[$serviceName] = $this->newEntry();
                }

                if (!isset($countedForService[$serviceName])) {
                    ++$revenue[$serviceName]['processes'];
                    $countedForService[$serviceName] = $serviceName;
                }
                $revenue[$serviceName]['collectiveFutureSavingsSum'] += $futureSavingsSum['sum'];
                $revenue[$serviceName]['collectiveRevenueSum'] += $futureSavingsSum['sum'];
                $revenue[$serviceName]['netCollectiveFutureSavingsSum'] += $futureSavingsSum['sum'] * $futureSavingsSum['netPercentage'] / 100.0;
                $revenue[$serviceName]['netCollectiveRevenueSum'] += $futureSavingsSum['sum'] * $futureSavingsSum['netPercentage'] / 100.0;
            }
        }

        foreach ($revenue as $key => $value) {
            $value['revenueAverage'] = $value['collectiveRevenueSum'] / $value['processes'];
            $value['netRevenueAverage'] = $value['netCollectiveRevenueSum'] / $value['processes'];

            $this->writeRow([
                $key,
                $this->formatNumber($value['processes'], 0),
                $this->formatNumber($value['collectiveRepaymentSum']),
                $this->formatNumber($value['collectiveFutureSavingsSum']),
                $this->formatNumber($value['collectiveRevenueSum']),
                $this->formatNumber($value['revenueAverage']),
                $this->formatNumber($value['netCollectiveRepaymentSum']),
                $this->formatNumber($value['netCollectiveFutureSavingsSum']),
                $this->formatNumber($value['netCollectiveRevenueSum']),
                $this->formatNumber($value['netRevenueAverage']),
            ]);
        }
    }

    /**
     * Create a new array entry.
     *
     * @return array
     */
    private function newEntry()
    {
        return [
            'processes' => 0,
            'collectiveRepaymentSum' => 0.0,
            'collectiveFutureSavingsSum' => 0.0,
            'collectiveRevenueSum' => 0.0,
            'netCollectiveRepaymentSum' => 0.0,
            'netCollectiveFutureSavingsSum' => 0.0,
            'netCollectiveRevenueSum' => 0.0,
        ];
    }

    /**
     * @return Process[]
     */
    private function getProcesses()
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)->createQueryBuilder('p')
            ->andWhere('p.completedAt IS NOT NULL');

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        $queryBuilder
            ->andWhere('p.completedAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return $queryBuilder->getQuery()->execute();
    }
}

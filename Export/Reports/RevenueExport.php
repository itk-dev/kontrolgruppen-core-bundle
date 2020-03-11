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
        ]);

        $revenue = [];

        foreach ($processes as $process) {
            $processRevenue = $this->economyService->calculateRevenue($process);

            if (!isset($revenue[$process->getService()->getName()])) {
                $revenue[$process->getService()->getName()] = [
                    'processes' => 0,
                    'collectiveRepaymentSum' => 0.0,
                    'collectiveFutureSavingsSum' => 0.0,
                    'collectiveRevenueSum' => 0.0,
                ];
            }

            ++$revenue[$process->getService()->getName()]['processes'];
            $revenue[$process->getService()->getName()]['collectiveRepaymentSum'] += $processRevenue['repaymentSum'];
            $revenue[$process->getService()->getName()]['collectiveFutureSavingsSum'] += $processRevenue['futureSavingsSum'];
            $revenue[$process->getService()->getName()]['collectiveRevenueSum'] = $processRevenue['repaymentSum'] + $processRevenue['futureSavingsSum'];
        }

        foreach ($revenue as $key => $value) {
            $value['revenueAverage'] = round($value['collectiveRevenueSum'] / $value['processes'], 2);

            $this->writeRow([
                $key,
                $value['processes'],
                $value['collectiveRepaymentSum'],
                $value['collectiveFutureSavingsSum'],
                $value['collectiveRevenueSum'],
                $value['revenueAverage'],
            ]);
        }
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

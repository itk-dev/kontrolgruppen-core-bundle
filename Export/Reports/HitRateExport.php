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
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Exception;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Psr\Cache\CacheItemPoolInterface;
use Traversable;

/**
 * Class HitRateExport.
 */
class HitRateExport extends AbstractExport
{
    protected $title = 'Hitrate';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var \Kontrolgruppen\CoreBundle\Service\EconomyService */
    private $economyService;

    /**
     * HitRateExport constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EconomyService         $economyService
     * @param CacheItemPoolInterface $cachePhpspreadsheet
     *
     * @throws Exception
     */
    public function __construct(EntityManagerInterface $entityManager, EconomyService $economyService, CacheItemPoolInterface $cachePhpspreadsheet)
    {
        parent::__construct($cachePhpspreadsheet);
        $this->entityManager = $entityManager;
        $this->economyService = $economyService;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return parent::getParameters();
    }

    /**
     * {@inheritdoc}
     */
    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->sheet->setTitle($this->title);

        $this->writeHeader([
            'Kanal',
            'Antal afsluttede sager',
            'Vundne',
            'Hitrate',
        ]);

        $hitRate = [];

        foreach ($processes as $process) {
            if (null !== $process->getChannel()) {
                $channelName = $process->getChannel()->getName();
                if (!isset($hitRate[$channelName])) {
                    $hitRate[$channelName] = [
                        'processes' => 0,
                        'won' => 0,
                    ];
                }

                ++$hitRate[$channelName]['processes'];
                $hitRate[$channelName]['won'] += $this->isProcessWon($process) ? 1 : 0;
            }
        }

        foreach ($hitRate as $key => $value) {
            $value['hitRate'] = $value['won'] / $value['processes'];

            $this->writeRow([
                $key,
                $this->formatNumber($value['processes'], 0),
                $this->formatNumber($value['won'], 0),
                $this->formatNumber($value['hitRate'] * 100).' %',
            ]);
        }
    }

    /**
     * @return Traversable
     *
     * @throws Exception
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

        return SimpleBatchIteratorAggregate::fromQuery(
            $queryBuilder->getQuery(),
            100
        );
    }

    /**
     * Decide if a process is won.
     *
     * @param Process $process
     *
     * @return bool
     */
    private function isProcessWon(Process $process)
    {
        $processRevenue = $this->economyService->calculateRevenue($process);

        return ($processRevenue['collectiveSum'] ?? 0) > 0;
    }
}

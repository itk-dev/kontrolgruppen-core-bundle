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

class HitRateExport extends AbstractExport
{
    protected $title = 'Hit rate';

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function getParameters()
    {
        return parent::getParameters();
    }

    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->sheet->setTitle($this->title);

        $this->writeHeader([
            'Kanal',
            'Antal afsluttede sager',
            'Vundne',
            'Hit rate',
        ]);

        $hitRate = [];

        foreach ($processes as $process) {
            if (!isset($hitRate[$process->getChannel()->getName()])) {
                $hitRate[$process->getChannel()->getName()] = [
                    'processes' => 0,
                    'won' => 0,
                ];
            }

            ++$hitRate[$process->getChannel()->getName()]['processes'];
            $hitRate[$process->getChannel()->getName()]['won'] +=
                $process->getCourtDecision() ? 1 : 0;
        }

        foreach ($hitRate as $key => $value) {
            $value['hitRate'] = $value['won'] / $value['processes'];

            $this->writeRow([
                $key,
                $value['processes'],
                $value['won'],
                ($value['hitRate'] * 100).' %',
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
            ->andWhere('p.createdAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return $queryBuilder->getQuery()->execute();
    }
}

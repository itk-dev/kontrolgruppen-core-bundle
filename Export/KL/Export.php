<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export\KL;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Export\AbstractExport;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Export extends AbstractExport
{
    protected $title = 'KL';

    public function getParameters()
    {
        return parent::getParameters() + [
                'processtatus' => [
                    'type' => ChoiceType::class,
                    'type_options' => [
                        'label' => 'Process status',
                        'choices' => [
                            'a' => 'A',
                        ],
                    ],
                ],
            ];
    }

    public function writeData()
    {
        $processes = $this->getProcesses();

        $this->writeRow([
            'Overføres ikke til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Overføres til KL',
            'Kun afsluttede sager overføres til KL',
            'Overføres ikke til KL',
        ]);

        $this->writeHeader([
            'Sag',
            'Kanal',
            'Sagstype',
            'Ydelsestype',
            'Er borgeren selvstændig erhvervsdrivende?',
            'Samlet tilbagebetalingskrav i kr.',
            'Samlet fremadrettet besparelse ved ydelsesstop i kr.',
            'Videresendes til anden myndighed',
            'Er sagen blevet politianmeldt?',
            'Status',
            'Diverse',
        ]);

        foreach ($processes as $process) {
            $this->writeRow([
                $process->getCaseNumber(),
                $process->getChannel() ? $process->getChannel()->getName() : null,
                $process->getProcessType() ? $process->getProcessType()->getName() : null,
                $process->getService() ? $process->getService()->getName() : null,
                $this->formatBoolean($process->getClient() && $process->getClient()->getSelfEmployed()),
                $this->formatAmount(0.0), // 'Samlet tilbagebetalingskrav i kr.',
                $this->formatAmount(0.0), // 'Samlet fremadrettet besparelse ved ydelsesstop i kr.',
                null, // 'Videresendes til anden myndighed',
                $this->formatBoolean($process->getPoliceReport()),
                $process->getProcessStatus() ? $process->getProcessStatus()->getName() : null,
                null,
            ]);
        }
    }

    /**
     * @return Process[]
     */
    private function getProcesses()
    {
        $queryBuilder = $this->entityManager->getRepository(Process::class)->createQueryBuilder('p');

        $startDate = $this->parameters['startdate'] ?? new \DateTime('2001-01-01');
        $endDate = $this->parameters['enddate'] ?? new \DateTime('2100-01-01');

        $queryBuilder
            ->andWhere('p.createdAt BETWEEN :startdate AND :enddate')
            ->setParameter('startdate', $startDate)
            ->setParameter('enddate', $endDate);

        return $queryBuilder->getQuery()->execute();
    }
}

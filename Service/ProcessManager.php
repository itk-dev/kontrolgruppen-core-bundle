<?php

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;

class ProcessManager
{
    private $processRepository;

    /**
     * ProcessManager constructor.
     * @param $processRepository
     */
    public function __construct(ProcessRepository $processRepository)
    {
        $this->processRepository = $processRepository;
    }

    /**
     * Create new process.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\Process|null $process
     * @return \Kontrolgruppen\CoreBundle\Entity\Process
     */
    public function newProcess(Process $process = null, ProcessType $processType = null) {
        if (null === $process) {
            $process = new Process();
            $process->setProcessType($processType);
        }
        $process->setCaseNumber($this->getNewCaseNumber());

        $conclusionClass = $process->getProcessType()->getConclusionClass();
        $conclusion = new $conclusionClass();
        $process->setConclusion($conclusion);

        return $process;
    }

    /**
     * Generate a new case number.
     * @return string case number of format YY-XXXX where YY is the year and XXXX an increasing counter
     */
    public function getNewCaseNumber()
    {
        $casesInYear = $this->processRepository->findAllFromYear(date('Y'));
        $caseNumber = str_pad(\count($casesInYear) + 1, 5, '0', STR_PAD_LEFT);

        return date('y').'-'.$caseNumber;
    }
}

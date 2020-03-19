<?php


namespace Kontrolgruppen\CoreBundle\Entity;


class RelatedProcess
{
    private $processGroup;
    private $process;
    private $isPrimary;

    public function __construct(ProcessGroup $processGroup, Process $process, bool $isPrimary)
    {
        $this->processGroup = $processGroup;
        $this->process = $process;
        $this->isPrimary = $isPrimary;
    }

    public function getProcessGroup(): ProcessGroup
    {
        return $this->processGroup;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getIsPrimary(): bool
    {
        return $this->isPrimary;
    }
}

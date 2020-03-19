<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

/**
 * Class RelatedProcess.
 */
class RelatedProcess
{
    private $processGroup;
    private $process;
    private $isPrimary;

    /**
     * RelatedProcess constructor.
     *
     * @param ProcessGroup $processGroup
     * @param Process      $process
     * @param bool         $isPrimary
     */
    public function __construct(ProcessGroup $processGroup, Process $process, bool $isPrimary)
    {
        $this->processGroup = $processGroup;
        $this->process = $process;
        $this->isPrimary = $isPrimary;
    }

    /**
     * Get process group.
     *
     * @return ProcessGroup
     */
    public function getProcessGroup(): ProcessGroup
    {
        return $this->processGroup;
    }

    /**
     * Get process.
     *
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * If the process represented in this object is the primary in the relationship.
     *
     * @return bool
     */
    public function getIsPrimary(): bool
    {
        return $this->isPrimary;
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\LogEntry;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessLogEntryRepository")
 */
class ProcessLogEntry extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="logEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    /**
     * @ORM\ManyToOne(targetEntity="Gedmo\Loggable\Entity\LogEntry")
     * @ORM\JoinColumn(nullable=false)
     */
    private $logEntry;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $level;

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    public function getLogEntry(): ?LogEntry
    {
        return $this->logEntry;
    }

    public function setLogEntry(?LogEntry $logEntry): self
    {
        $this->logEntry = $logEntry;

        return $this;
    }
}

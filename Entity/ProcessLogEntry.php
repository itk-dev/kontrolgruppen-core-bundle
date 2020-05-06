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
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Loggable\Entity\LogEntry;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessLogEntryRepository")
 */
class ProcessLogEntry extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="logEntries")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $process;

    /**
     * @ORM\ManyToOne(targetEntity="Gedmo\Loggable\Entity\LogEntry")
     * @ORM\JoinColumn(nullable=false)
     */
    private $logEntry;

    /**
     * @ORM\Column(name="level", type="ProcessLogEntryLevelEnumType", nullable=false)
     *
     * @DoctrineAssert\Enum(entity="Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType")
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     */
    private $creatorName;

    /**
     * @return string|null
     */
    public function getLevel(): ?string
    {
        return $this->level;
    }

    /**
     * @param string $level
     *
     * @return ProcessLogEntry
     */
    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Process|null
     */
    public function getProcess(): ?Process
    {
        return $this->process;
    }

    /**
     * @param Process|null $process
     *
     * @return ProcessLogEntry
     */
    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return LogEntry|null
     */
    public function getLogEntry(): ?LogEntry
    {
        return $this->logEntry;
    }

    /**
     * @param LogEntry|null $logEntry
     *
     * @return ProcessLogEntry
     */
    public function setLogEntry(?LogEntry $logEntry): self
    {
        $this->logEntry = $logEntry;

        return $this;
    }

    /**
     * Set creator name.
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setCreatorName(?string $name): self
    {
        $this->creatorName = $name;

        return $this;
    }

    /**
     * Get creator name.
     *
     * @return string|null
     */
    public function getCreatorName(): ?string
    {
        return $this->creatorName;
    }
}

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
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessLogEntryRepository")
 */
class ProcessLogEntry extends AbstractLogEntry
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="logEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

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
}

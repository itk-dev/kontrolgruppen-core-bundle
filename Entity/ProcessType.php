<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository")
 */
class ProcessType extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="processType")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus", inversedBy="processTypes")
     */
    private $processStatuses;

    public function __construct()
    {
        $this->processes = new ArrayCollection();
        $this->processStatuses = new ArrayCollection();
    }

    /**
     * @return Collection|Process[]
     */
    public function getProcesses(): Collection
    {
        return $this->processes;
    }

    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
            $process->setProcessType($this);
        }

        return $this;
    }

    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getProcessType() === $this) {
                $process->setProcessType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProcessStatus[]
     */
    public function getProcessStatuses(): Collection
    {
        return $this->processStatuses;
    }

    public function addProcessStatus(ProcessStatus $processStatus): self
    {
        if (!$this->processStatuses->contains($processStatus)) {
            $this->processStatuses[] = $processStatus;
        }

        return $this;
    }

    public function removeProcessStatus(ProcessStatus $processStatus): self
    {
        if ($this->processStatuses->contains($processStatus)) {
            $this->processStatuses->removeElement($processStatus);
        }

        return $this;
    }
}

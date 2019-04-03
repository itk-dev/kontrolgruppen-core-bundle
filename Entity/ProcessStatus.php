<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessStatusRepository")
 */
class ProcessStatus extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="processStatus")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", mappedBy="processStatuses")
     */
    private $processTypes;

    public function __construct()
    {
        $this->processes = new ArrayCollection();
        $this->processTypes = new ArrayCollection();
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
            $process->setProcessStatus($this);
        }

        return $this;
    }

    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getProcessStatus() === $this) {
                $process->setProcessStatus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProcessType[]
     */
    public function getProcessTypes(): Collection
    {
        return $this->processTypes;
    }

    public function addProcessType(ProcessType $processType): self
    {
        if (!$this->processTypes->contains($processType)) {
            $this->processTypes[] = $processType;
            $processType->addProcessStatus($this);
        }

        return $this;
    }

    public function removeProcessType(ProcessType $processType): self
    {
        if ($this->processTypes->contains($processType)) {
            $this->processTypes->removeElement($processType);
            $processType->removeProcessStatus($this);
        }

        return $this;
    }
}
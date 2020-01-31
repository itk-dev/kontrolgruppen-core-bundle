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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository")
 *
 * @Gedmo\Loggable()
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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $conclusionClass;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hideInDashboard;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service", inversedBy="processTypes")
     */
    private $services;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Channel", inversedBy="processTypes")
     */
    private $channels;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $defaultProcessStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus")
     * @ORM\JoinColumn(nullable=false)
     */
    private $defaultProcessStatusOnEmptyCaseWorker;

    public function __construct()
    {
        $this->processes = new ArrayCollection();
        $this->processStatuses = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->channels = new ArrayCollection();
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

    public function getConclusionClass(): ?string
    {
        return $this->conclusionClass;
    }

    public function setConclusionClass(string $conclusionClass): self
    {
        $this->conclusionClass = $conclusionClass;

        return $this;
    }

    public function getHideInDashboard(): ?bool
    {
        return $this->hideInDashboard;
    }

    public function setHideInDashboard(?bool $hideInDashboard): self
    {
        $this->hideInDashboard = $hideInDashboard;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
        }

        return $this;
    }

    /**
     * @return Collection|Channel[]
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(Channel $channel): self
    {
        if (!$this->channels->contains($channel)) {
            $this->channels[] = $channel;
        }

        return $this;
    }

    public function removeChannel(Channel $channel): self
    {
        if ($this->channels->contains($channel)) {
            $this->channels->removeElement($channel);
        }

        return $this;
    }

    public function getDefaultProcessStatus(): ?ProcessStatus
    {
        return $this->defaultProcessStatus;
    }

    public function setDefaultProcessStatus(?ProcessStatus $defaultProcessStatus): self
    {
        $this->defaultProcessStatus = $defaultProcessStatus;

        return $this;
    }

    public function getDefaultProcessStatusOnEmptyCaseWorker(): ?ProcessStatus
    {
        return $this->defaultProcessStatusOnEmptyCaseWorker;
    }

    public function setDefaultProcessStatusOnEmptyCaseWorker(?ProcessStatus $defaultProcessStatusOnEmptyCaseWorker): self
    {
        $this->defaultProcessStatusOnEmptyCaseWorker = $defaultProcessStatusOnEmptyCaseWorker;

        return $this;
    }
}

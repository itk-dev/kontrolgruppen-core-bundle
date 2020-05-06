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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ChannelRepository")
 *
 * @Gedmo\Loggable()
 */
class Channel extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="channel")
     */
    private $processes;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", mappedBy="channels")
     */
    private $processTypes;

    /**
     * Channel constructor.
     */
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

    /**
     * @param Process $process
     *
     * @return Channel
     */
    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
            $process->setChannel($this);
        }

        return $this;
    }

    /**
     * @param Process $process
     *
     * @return Channel
     */
    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getChannel() === $this) {
                $process->setChannel(null);
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

    /**
     * @param ProcessType $processType
     *
     * @return Channel
     */
    public function addProcessType(ProcessType $processType): self
    {
        if (!$this->processTypes->contains($processType)) {
            $this->processTypes[] = $processType;
            $processType->addChannel($this);
        }

        return $this;
    }

    /**
     * @param ProcessType $processType
     *
     * @return $this
     */
    public function removeProcessType(ProcessType $processType): self
    {
        if ($this->processTypes->contains($processType)) {
            $this->processTypes->removeElement($processType);
            $processType->removeChannel($this);
        }

        return $this;
    }
}

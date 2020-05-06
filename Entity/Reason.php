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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ReasonRepository")
 *
 * @Gedmo\Loggable()
 */
class Reason extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="reason")
     */
    private $processes;

    /**
     * Reason constructor.
     */
    public function __construct()
    {
        $this->processes = new ArrayCollection();
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
     * @return $this
     */
    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
            $process->setReason($this);
        }

        return $this;
    }

    /**
     * @param Process $process
     *
     * @return $this
     */
    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getReason() === $this) {
                $process->setReason(null);
            }
        }

        return $this;
    }
}

<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ServiceRepository")
 */
class Service extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="service")
     */
    private $process;

    public function __construct()
    {
        $this->process = new ArrayCollection();
    }

    /**
     * @return Collection|Process[]
     */
    public function getProcess(): Collection
    {
        return $this->process;
    }

    public function addProcess(Process $process): self
    {
        if (!$this->process->contains($process)) {
            $this->process[] = $process;
            $process->setService($this);
        }

        return $this;
    }

    public function removeProcess(Process $process): self
    {
        if ($this->process->contains($process)) {
            $this->process->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getService() === $this) {
                $process->setService(null);
            }
        }

        return $this;
    }
}

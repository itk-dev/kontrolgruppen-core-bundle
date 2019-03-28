<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ServiceRepository")
 */
class Service
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="service")
     */
    private $process;

    public function __construct()
    {
        $this->process = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessGroupRepository")
 */
class ProcessGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="processGroups")
     */
    private $processes;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process")
     * @ORM\JoinColumn(nullable=false)
     */
    private $primaryProcess;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDefault;

    /**
     * ProcessGroup constructor.
     */
    public function __construct()
    {
        $this->processes = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get processes.
     *
     * @return Collection|Process[]
     */
    public function getProcesses(): Collection
    {
        return $this->processes;
    }

    /**
     * Add process.
     *
     * @param Process $process
     *
     * @return $this
     */
    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
        }

        return $this;
    }

    /**
     * Remove process.
     *
     * @param Process $process
     *
     * @return $this
     */
    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
        }

        return $this;
    }

    /**
     * Get primary process.
     *
     * @return Process|null
     */
    public function getPrimaryProcess(): ?Process
    {
        return $this->primaryProcess;
    }

    /**
     * Set primary process.
     *
     * @param Process|null $primaryProcess
     *
     * @return $this
     */
    public function setPrimaryProcess(?Process $primaryProcess): self
    {
        $this->primaryProcess = $primaryProcess;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get is default.
     *
     * @return bool|null
     */
    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * Set is default.
     *
     * @param bool $isDefault
     *
     * @return $this
     */
    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}

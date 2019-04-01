<?php

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
    private $process_type;

    public function __construct()
    {
        $this->process_type = new ArrayCollection();
    }

    /**
     * @return Collection|Process[]
     */
    public function getProcess_type(): Collection
    {
        return $this->process_type;
    }

    public function addCase(Process $case): self
    {
        if (!$this->process_type->contains($case)) {
            $this->process_type[] = $case;
            $case->setProcessType($this);
        }

        return $this;
    }

    public function removeCase(Process $case): self
    {
        if ($this->process_type->contains($case)) {
            $this->process_type->removeElement($case);
            // set the owning side to null (unless already changed)
            if ($case->getProcessType() === $this) {
                $case->setProcessType(null);
            }
        }

        return $this;
    }
}

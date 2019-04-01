<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\CaseTypeRepository")
 */
class CaseType extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="caseType")
     */
    private $cases;

    public function __construct()
    {
        $this->cases = new ArrayCollection();
    }

    /**
     * @return Collection|Process[]
     */
    public function getCases(): Collection
    {
        return $this->cases;
    }

    public function addCase(Process $case): self
    {
        if (!$this->cases->contains($case)) {
            $this->cases[] = $case;
            $case->setCaseType($this);
        }

        return $this;
    }

    public function removeCase(Process $case): self
    {
        if ($this->cases->contains($case)) {
            $this->cases->removeElement($case);
            // set the owning side to null (unless already changed)
            if ($case->getCaseType() === $this) {
                $case->setCaseType(null);
            }
        }

        return $this;
    }
}

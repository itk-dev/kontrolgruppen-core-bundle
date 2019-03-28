<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\CaseTypeRepository")
 */
class CaseType
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
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="caseType")
     */
    private $cases;

    public function __construct()
    {
        $this->cases = new ArrayCollection();
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

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getName();
    }
}

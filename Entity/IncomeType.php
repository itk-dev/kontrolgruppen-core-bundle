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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\IncomeTypeRepository")
 *
 * @Gedmo\Loggable()
 */
class IncomeType extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\IncomeEconomyEntry", mappedBy="incomeType")
     */
    private $incomeEconomyEntries;

    /**
     * IncomeType constructor.
     */
    public function __construct()
    {
        $this->incomeEconomyEntries = new ArrayCollection();
    }

    /**
     * @return Collection|IncomeEconomyEntry[]
     */
    public function getIncomeEconomyEntries(): Collection
    {
        return $this->incomeEconomyEntries;
    }

    /**
     * @param IncomeEconomyEntry $incomeEconomyEntry
     *
     * @return IncomeType
     */
    public function addIncomeEconomyEntry(IncomeEconomyEntry $incomeEconomyEntry): self
    {
        if (!$this->incomeEconomyEntries->contains($incomeEconomyEntry)) {
            $this->incomeEconomyEntries[] = $incomeEconomyEntry;
            $incomeEconomyEntry->setIncomeType($this);
        }

        return $this;
    }

    /**
     * @param IncomeEconomyEntry $incomeEconomyEntry
     *
     * @return IncomeType
     */
    public function removeIncomeEconomyEntry(IncomeEconomyEntry $incomeEconomyEntry): self
    {
        if ($this->incomeEconomyEntries->contains($incomeEconomyEntry)) {
            $this->incomeEconomyEntries->removeElement($incomeEconomyEntry);
            // set the owning side to null (unless already changed)
            if ($incomeEconomyEntry->getIncomeType() === $this) {
                $incomeEconomyEntry->setIncomeType(null);
            }
        }

        return $this;
    }
}

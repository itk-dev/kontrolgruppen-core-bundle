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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\AccountRepository")
 *
 * @Gedmo\Loggable()
 */
class Account extends AbstractTaxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\BaseEconomyEntry", mappedBy="account")
     */
    private $baseEconomyEntries;

    /**
     * Account constructor.
     */
    public function __construct()
    {
        $this->baseEconomyEntries = new ArrayCollection();
    }

    /**
     * @return Collection|BaseEconomyEntry[]
     */
    public function getBaseEconomyEntries(): Collection
    {
        return $this->baseEconomyEntries;
    }

    /**
     * @param BaseEconomyEntry $baseEconomyEntry
     *
     * @return Account
     */
    public function addBaseEconomyEntry(BaseEconomyEntry $baseEconomyEntry): self
    {
        if (!$this->baseEconomyEntries->contains($baseEconomyEntry)) {
            $this->baseEconomyEntries[] = $baseEconomyEntry;
            $baseEconomyEntry->setAccount($this);
        }

        return $this;
    }

    /**
     * @param BaseEconomyEntry $baseEconomyEntry
     *
     * @return Account
     */
    public function removeBaseEconomyEntry(BaseEconomyEntry $baseEconomyEntry): self
    {
        if ($this->baseEconomyEntries->contains($baseEconomyEntry)) {
            $this->baseEconomyEntries->removeElement($baseEconomyEntry);
            // set the owning side to null (unless already changed)
            if ($baseEconomyEntry->getAccount() === $this) {
                $baseEconomyEntry->setAccount(null);
            }
        }

        return $this;
    }
}

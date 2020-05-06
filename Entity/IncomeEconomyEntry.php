<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 *
 * @Gedmo\Loggable()
 */
class IncomeEconomyEntry extends EconomyEntry
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $periodFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $periodTo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Gedmo\Versioned()
     *
     * How many months does the amount cover. Defaults to 1.
     */
    private $amountPeriod;

    /**
     * @ORM\ManyToOne(targetEntity="\Kontrolgruppen\CoreBundle\Entity\IncomeType", inversedBy="incomeEconomyEntries")
     */
    private $incomeType;

    /**
     * @return \DateTime|null
     */
    public function getPeriodFrom(): ?\DateTime
    {
        return $this->periodFrom;
    }

    /**
     * @param \DateTime|null $periodFrom
     *
     * @return IncomeEconomyEntry
     */
    public function setPeriodFrom(?\DateTime $periodFrom): self
    {
        $this->periodFrom = $periodFrom;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPeriodTo(): ?\DateTime
    {
        return $this->periodTo;
    }

    /**
     * @param \DateTime|null $periodTo
     *
     * @return IncomeEconomyEntry
     */
    public function setPeriodTo(?\DateTime $periodTo): self
    {
        $this->periodTo = $periodTo;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAmountPeriod(): ?int
    {
        return $this->amountPeriod;
    }

    /**
     * @param int|null $amountPeriod
     *
     * @return IncomeEconomyEntry
     */
    public function setAmountPeriod(?int $amountPeriod): self
    {
        $this->amountPeriod = $amountPeriod;

        return $this;
    }

    /**
     * @return IncomeType|null
     */
    public function getIncomeType(): ?IncomeType
    {
        return $this->incomeType;
    }

    /**
     * @param IncomeType|null $incomeType
     *
     * @return IncomeEconomyEntry
     */
    public function setIncomeType(?IncomeType $incomeType): self
    {
        $this->incomeType = $incomeType;

        return $this;
    }
}

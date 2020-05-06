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
use Kontrolgruppen\CoreBundle\Validator\FutureSavings;
use Kontrolgruppen\CoreBundle\Validator\Repayment;

/**
 * @ORM\Entity
 *
 * @Gedmo\Loggable()
 *
 * @FutureSavings
 *
 * @Repayment
 */
class ServiceEconomyEntry extends EconomyEntry
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service")
     * @ORM\JoinColumn(nullable=true)
     */
    private $service;

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
     * @ORM\Column(type="float", nullable=true)
     */
    private $futureSavingsAmount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $futureSavingsPeriodFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $futureSavingsPeriodTo;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $repaymentAmount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $repaymentPeriodFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $repaymentPeriodTo;

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service|null $service
     *
     * @return ServiceEconomyEntry
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

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
     * @return ServiceEconomyEntry
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
     * @return ServiceEconomyEntry
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
     * @return ServiceEconomyEntry
     */
    public function setAmountPeriod(?int $amountPeriod): self
    {
        $this->amountPeriod = $amountPeriod;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getFutureSavingsAmount(): ?float
    {
        return $this->futureSavingsAmount;
    }

    /**
     * @param float $futureSavingsAmount
     *
     * @return ServiceEconomyEntry
     */
    public function setFutureSavingsAmount(float $futureSavingsAmount): self
    {
        $this->futureSavingsAmount = $futureSavingsAmount;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFutureSavingsPeriodFrom(): ?\DateTimeInterface
    {
        return $this->futureSavingsPeriodFrom;
    }

    /**
     * @param \DateTimeInterface|null $futureSavingsPeriodFrom
     *
     * @return ServiceEconomyEntry
     */
    public function setFutureSavingsPeriodFrom(?\DateTimeInterface $futureSavingsPeriodFrom): self
    {
        $this->futureSavingsPeriodFrom = $futureSavingsPeriodFrom;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getFutureSavingsPeriodTo(): ?\DateTimeInterface
    {
        return $this->futureSavingsPeriodTo;
    }

    /**
     * @param \DateTimeInterface|null $futureSavingsPeriodTo
     *
     * @return ServiceEconomyEntry
     */
    public function setFutureSavingsPeriodTo(?\DateTimeInterface $futureSavingsPeriodTo): self
    {
        $this->futureSavingsPeriodTo = $futureSavingsPeriodTo;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRepaymentAmount(): ?float
    {
        return $this->repaymentAmount;
    }

    /**
     * @param float $repaymentAmount
     *
     * @return ServiceEconomyEntry
     */
    public function setRepaymentAmount(float $repaymentAmount): self
    {
        $this->repaymentAmount = $repaymentAmount;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getRepaymentPeriodFrom(): ?\DateTimeInterface
    {
        return $this->repaymentPeriodFrom;
    }

    /**
     * @param \DateTimeInterface|null $repaymentPeriodFrom
     *
     * @return ServiceEconomyEntry
     */
    public function setRepaymentPeriodFrom(?\DateTimeInterface $repaymentPeriodFrom): self
    {
        $this->repaymentPeriodFrom = $repaymentPeriodFrom;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getRepaymentPeriodTo(): ?\DateTimeInterface
    {
        return $this->repaymentPeriodTo;
    }

    /**
     * @param \DateTimeInterface|null $repaymentPeriodTo
     *
     * @return ServiceEconomyEntry
     */
    public function setRepaymentPeriodTo(?\DateTimeInterface $repaymentPeriodTo): self
    {
        $this->repaymentPeriodTo = $repaymentPeriodTo;

        return $this;
    }
}

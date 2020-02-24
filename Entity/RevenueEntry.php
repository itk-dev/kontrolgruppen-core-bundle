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
use Kontrolgruppen\CoreBundle\DBAL\Types\RevenueFutureTypeEnumType;
use Kontrolgruppen\CoreBundle\DBAL\Types\RevenueTypeEnumType;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\RevenueEntryRepository")
 */
class RevenueEntry extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="revenueEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service", inversedBy="revenueEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="RevenueTypeEnumType")
     */
    private $type;

    /**
     * @ORM\Column(type="RevenueFutureTypeEnumType", nullable=true)
     */
    private $futureSavingsType;

    /**
     * @return Process|null
     */
    public function getProcess(): ?Process
    {
        return $this->process;
    }

    /**
     * @param Process|null $process
     *
     * @return RevenueEntry
     */
    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }

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
     * @return RevenueEntry
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return RevenueEntry
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return RevenueEntry
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFutureSavingsType(): ?string
    {
        return $this->futureSavingsType;
    }

    /**
     * @param $futureSavingsType
     *
     * @return RevenueEntry
     */
    public function setFutureSavingsType($futureSavingsType): self
    {
        $this->futureSavingsType = $futureSavingsType;

        return $this;
    }
}

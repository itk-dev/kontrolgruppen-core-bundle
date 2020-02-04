<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\RevenueEntryRepository")
 */
class RevenueEntry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFutureSavingsType()
    {
        return $this->futureSavingsType;
    }

    /**
     * @param $futureSavingsType
     *
     * @return $this
     */
    public function setFutureSavingsType($futureSavingsType): self
    {
        $this->futureSavingsType = $futureSavingsType;

        return $this;
    }
}

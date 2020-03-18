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
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 *
 * @Gedmo\Loggable()
 */
class EconomyEntry extends AbstractEntity implements ProcessLoggableInterface
{
    /**
     * @ORM\Column(type="float", nullable=false)
     *
     * @Gedmo\Versioned()
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="economyEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    /**
     * @ORM\Column(name="type", type="EconomyEntryEnumType", nullable=false)
     *
     * @DoctrineAssert\Enum(entity="Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryEnumType")
     *
     * @Gedmo\Versioned()
     */
    private $type;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return EconomyEntry
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
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
     * @return EconomyEntry
     */
    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     *
     * @return EconomyEntry
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }
}

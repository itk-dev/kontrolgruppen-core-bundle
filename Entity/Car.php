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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\CarRepository")
 *
 * @Gedmo\Loggable()
 */
class Car extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Gedmo\Versioned()
     */
    private $registrationNumber;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $sharedOwnership;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Client", inversedBy="cars")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    public function getSharedOwnership(): ?bool
    {
        return $this->sharedOwnership;
    }

    public function setSharedOwnership(?bool $sharedOwnership): self
    {
        $this->sharedOwnership = $sharedOwnership;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}

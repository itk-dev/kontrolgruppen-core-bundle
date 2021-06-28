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
 * Base class for clients on a process.
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
abstract class AbstractProcessClient extends AbstractEntity
{
    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="client", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $telephone;

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return Client
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string|null $postalCode
     *
     * @return AbstractProcessClient
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return AbstractProcessClient
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     *
     * @return AbstractProcessClient
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }
}

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
use Kontrolgruppen\CoreBundle\Validator as KontrolgruppenAssert;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessClientPersonRepository")
 */
class ProcessClientPerson extends AbstractProcessClient
{
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @KontrolgruppenAssert\CPR
     *
     * @Gedmo\Versioned()
     */
    private $cpr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $numberOfChildren;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $receivesPublicAid;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $employed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $hasOwnCompany;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasDriversLicense;

    public function getCpr(): ?string
    {
        return $this->cpr;
    }

    public function setCpr(string $cpr): self
    {
        $this->cpr = $cpr;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNumberOfChildren(): ?int
    {
        return $this->numberOfChildren;
    }

    public function setNumberOfChildren(int $numberOfChildren): self
    {
        $this->numberOfChildren = $numberOfChildren;

        return $this;
    }

    public function getReceivesPublicAid(): ?bool
    {
        return $this->receivesPublicAid;
    }

    public function setReceivesPublicAid(bool $receivesPublicAid): self
    {
        $this->receivesPublicAid = $receivesPublicAid;

        return $this;
    }

    public function getEmployed(): ?bool
    {
        return $this->employed;
    }

    public function setEmployed(bool $employed): self
    {
        $this->employed = $employed;

        return $this;
    }

    public function getHasOwnCompany(): ?bool
    {
        return $this->hasOwnCompany;
    }

    public function setHasOwnCompany(bool $hasOwnCompany): self
    {
        $this->hasOwnCompany = $hasOwnCompany;

        return $this;
    }

    public function getHasDriversLicense(): ?bool
    {
        return $this->hasDriversLicense;
    }

    public function setHasDriversLicense(bool $hasDriversLicense): self
    {
        $this->hasDriversLicense = $hasDriversLicense;

        return $this;
    }
}

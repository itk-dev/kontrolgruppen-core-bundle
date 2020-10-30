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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ClientRepository")
 *
 * @Gedmo\Loggable()
 */
class Client extends AbstractEntity implements ProcessLoggableInterface
{
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
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $numberOfChildren;

    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="client", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

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

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasCar;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Car", mappedBy="client", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $cars;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Company", mappedBy="client", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $companies;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return Client
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     *
     * @return Client
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

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
     * @return Client
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
     * @return Client
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
     * @return Client
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberOfChildren(): ?int
    {
        return $this->numberOfChildren;
    }

    /**
     * @param int|null $numberOfChildren
     *
     * @return Client
     */
    public function setNumberOfChildren(?int $numberOfChildren): self
    {
        $this->numberOfChildren = $numberOfChildren;

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
     * @param Process $process
     *
     * @return Client
     */
    public function setProcess(Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasOwnCompany(): ?bool
    {
        return $this->hasOwnCompany;
    }

    /**
     * @param bool|null $hasOwnCompany
     *
     * @return Client
     */
    public function setHasOwnCompany(?bool $hasOwnCompany): self
    {
        $this->hasOwnCompany = $hasOwnCompany;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getReceivesPublicAid(): ?bool
    {
        return $this->receivesPublicAid;
    }

    /**
     * @param bool|null $receivesPublicAid
     *
     * @return Client
     */
    public function setReceivesPublicAid(?bool $receivesPublicAid): self
    {
        $this->receivesPublicAid = $receivesPublicAid;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEmployed(): ?bool
    {
        return $this->employed;
    }

    /**
     * @param bool|null $employed
     *
     * @return Client
     */
    public function setEmployed(?bool $employed): self
    {
        $this->employed = $employed;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasDriversLicense(): ?bool
    {
        return $this->hasDriversLicense;
    }

    /**
     * @param bool|null $hasDriversLicense
     *
     * @return Client
     */
    public function setHasDriversLicense(?bool $hasDriversLicense): self
    {
        $this->hasDriversLicense = $hasDriversLicense;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasCar(): ?bool
    {
        return $this->hasCar;
    }

    /**
     * @param bool|null $hasCar
     *
     * @return Client
     */
    public function setHasCar(?bool $hasCar): self
    {
        $this->hasCar = $hasCar;

        return $this;
    }

    /**
     * @return Collection|Car[]
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    /**
     * @param Car $car
     *
     * @return Client
     */
    public function addCar(Car $car): self
    {
        if (!$this->cars->contains($car)) {
            $this->cars[] = $car;
            $car->setClient($this);
        }

        return $this;
    }

    /**
     * @param Car $car
     *
     * @return Client
     */
    public function removeCar(Car $car): self
    {
        if ($this->cars->contains($car)) {
            $this->cars->removeElement($car);
            // set the owning side to null (unless already changed)
            if ($car->getClient() === $this) {
                $car->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @param Company $company
     *
     * @return $this
     */
    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setClient($this);
        }

        return $this;
    }

    /**
     * @param Company $company
     *
     * @return $this
     */
    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            // set the owning side to null (unless already changed)
            if ($company->getClient() === $this) {
                $company->setClient(null);
            }
        }

        return $this;
    }
}

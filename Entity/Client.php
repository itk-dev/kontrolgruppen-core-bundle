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
 * @Gedmo\Loggable()
 */
class Client extends AbstractEntity implements ProcessLoggableInterface
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned()
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer", nullable=true)
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
     */
    private $selfEmployed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $worksInMajorCompany;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $notEmployed;

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

    public function __construct()
    {
        $this->cars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNumberOfChildren(): ?int
    {
        return $this->numberOfChildren;
    }

    public function setNumberOfChildren(?int $numberOfChildren): self
    {
        $this->numberOfChildren = $numberOfChildren;

        return $this;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    public function getSelfEmployed(): ?bool
    {
        return $this->selfEmployed;
    }

    public function setSelfEmployed(?bool $selfEmployed): self
    {
        $this->selfEmployed = $selfEmployed;

        return $this;
    }

    public function getWorksInMajorCompany(): ?bool
    {
        return $this->worksInMajorCompany;
    }

    public function setWorksInMajorCompany(?bool $worksInMajorCompany): self
    {
        $this->worksInMajorCompany = $worksInMajorCompany;

        return $this;
    }

    public function getNotEmployed(): ?bool
    {
        return $this->notEmployed;
    }

    public function setNotEmployed(?bool $notEmployed): self
    {
        $this->notEmployed = $notEmployed;

        return $this;
    }

    public function getHasDriversLicense(): ?bool
    {
        return $this->hasDriversLicense;
    }

    public function setHasDriversLicense(?bool $hasDriversLicense): self
    {
        $this->hasDriversLicense = $hasDriversLicense;

        return $this;
    }

    public function getHasCar(): ?bool
    {
        return $this->hasCar;
    }

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

    public function addCar(Car $car): self
    {
        if (!$this->cars->contains($car)) {
            $this->cars[] = $car;
            $car->setClient($this);
        }

        return $this;
    }

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
}

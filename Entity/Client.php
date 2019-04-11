<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ClientRepository")
 */
class Client extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cpr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfChildren;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $carRegistrationNumber;

    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="client", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    public function getId(): ?int
    {
        return $this->id;
    }

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
        $this->address = $Address;

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

    public function getCarRegistrationNumber(): ?string
    {
        return $this->carRegistrationNumber;
    }

    public function setCarRegistrationNumber(?string $carRegistrationNumber): self
    {
        $this->carRegistrationNumber = $carRegistrationNumber;

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
}

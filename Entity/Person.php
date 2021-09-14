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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\PersonRepository")
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $CPR;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany", inversedBy="people")
     * @ORM\JoinColumn(nullable=false)
     */
    private $processClient;

    /**
     * @ORM\Column(type="boolean")
     */
    private $highlighted;

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
    public function getCPR(): ?string
    {
        return $this->CPR;
    }

    /**
     * @param string $cpr
     *
     * @return $this
     */
    public function setCPR(string $cpr): self
    {
        $this->CPR = $cpr;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ProcessClientCompany|null
     */
    public function getProcessClient(): ?ProcessClientCompany
    {
        return $this->processClient;
    }

    /**
     * @param ProcessClientCompany|null $processClient
     *
     * @return $this
     */
    public function setProcessClient(?ProcessClientCompany $processClient): self
    {
        $this->processClient = $processClient;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isHighlighted(): ?bool
    {
        return $this->highlighted;
    }

    /**
     * @param bool $highlighted
     *
     * @return $this
     */
    public function setHighlighted(bool $highlighted): self
    {
        $this->highlighted = $highlighted;

        return $this;
    }
}

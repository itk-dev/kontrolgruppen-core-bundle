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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessClientCompanyRepository")
 *
 * @Gedmo\Loggable()
 */
class ProcessClientCompany extends AbstractProcessClient
{
    public const TYPE = parent::COMPANY;
    protected $type = parent::COMPANY;

    /**
     * @ORM\Embedded(class="Kontrolgruppen\CoreBundle\Entity\ContactPerson")
     */
    private $contactPerson;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Person", mappedBy="processClient", orphanRemoval=true, cascade={"persist", "remove"})
     *
     * @Assert\Valid()
     */
    private $people;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->contactPerson = new ContactPerson();
        $this->people = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getCvr() ?? parent::__toString();
    }

    /**
     * Get cvr.
     *
     * @return string|null
     */
    public function getCvr(): ?string
    {
        return $this->getIdentifier();
    }

    /**
     * Set svr.
     *
     * @param string $cvr
     *
     * @return $this
     */
    public function setCvr(string $cvr): self
    {
        return $this->setIdentifier($cvr);
    }

    /**
     * @return ContactPerson
     */
    public function getContactPerson(): ContactPerson
    {
        return $this->contactPerson;
    }

    /**
     * @param ContactPerson $contactPerson
     *
     * @return $this
     */
    public function setContactPerson(ContactPerson $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    /**
     * @param Person $person
     *
     * @return $this
     */
    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->setProcessClient($this);
        }

        return $this;
    }

    /**
     * @param Person $person
     *
     * @return $this
     */
    public function removePerson(Person $person): self
    {
        if ($this->people->contains($person)) {
            $this->people->removeElement($person);
            // set the owning side to null (unless already changed)
            if ($person->getProcessClient() === $this) {
                $person->setProcessClient(null);
            }
        }

        return $this;
    }
}

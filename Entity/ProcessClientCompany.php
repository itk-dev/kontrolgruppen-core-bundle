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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessClientCompanyRepository")
 */
class ProcessClientCompany extends AbstractProcessClient
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cvr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $name;

    public function getCvr(): ?string
    {
        return $this->cvr;
    }

    public function setCvr(string $cvr): self
    {
        $this->cvr = $cvr;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

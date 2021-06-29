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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("name")
 */
abstract class AbstractTaxonomy extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $clientType;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Gedmo\Versioned
     */
    protected $name;

    /**
     * @return string|null
     */
    public function getClientType(): ?string
    {
        return $this->clientType;
    }

    /**
     * @param mixed $clientType
     *
     * @return AbstractTaxonomy
     */
    public function setClientType(string $clientType): self
    {
        if (null !== $this->clientType && $clientType !== $this->clientType) {
            throw new \RuntimeException('Cannot change client type');
        }

        $this->clientType = $clientType;

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
     * @return AbstractTaxonomy
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
    }
}

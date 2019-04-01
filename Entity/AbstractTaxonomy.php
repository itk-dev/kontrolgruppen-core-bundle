<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractTaxonomy extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getName();
    }
}

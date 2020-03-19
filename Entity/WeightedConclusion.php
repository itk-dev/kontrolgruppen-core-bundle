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
 * @ORM\Entity
 *
 * @Gedmo\Loggable()
 */
class WeightedConclusion extends Conclusion
{
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $argumentsFor;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $argumentsAgainst;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $conclusion;

    /**
     * @return string|null
     */
    public function getArgumentsFor(): ?string
    {
        return $this->argumentsFor;
    }

    /**
     * @param string|null $argumentsFor
     *
     * @return WeightedConclusion
     */
    public function setArgumentsFor(?string $argumentsFor): self
    {
        $this->argumentsFor = $argumentsFor;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getArgumentsAgainst(): ?string
    {
        return $this->argumentsAgainst;
    }

    /**
     * @param string|null $argumentsAgainst
     *
     * @return WeightedConclusion
     */
    public function setArgumentsAgainst(?string $argumentsAgainst): self
    {
        $this->argumentsAgainst = $argumentsAgainst;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getConclusion(): ?string
    {
        return $this->conclusion;
    }

    /**
     * @param string|null $conclusion
     *
     * @return WeightedConclusion
     */
    public function setConclusion(?string $conclusion): self
    {
        $this->conclusion = $conclusion;

        return $this;
    }
}

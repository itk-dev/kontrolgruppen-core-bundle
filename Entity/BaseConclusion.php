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
class BaseConclusion extends Conclusion
{
    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $conclusion;

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
     * @return BaseConclusion
     */
    public function setConclusion(?string $conclusion): self
    {
        $this->conclusion = $conclusion;

        return $this;
    }
}

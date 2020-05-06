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

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ConclusionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 *
 * This is an empty conclusion type, which should be inherited from for different
 * conclusion types. For example, see BaseConclusion.
 */
class Conclusion extends AbstractEntity implements ProcessLoggableInterface
{
    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="conclusion", cascade={"persist", "remove"})
     */
    private $process;

    /**
     * @return Process|null
     */
    public function getProcess(): ?Process
    {
        return $this->process;
    }

    /**
     * @param Process|null $process
     *
     * @return $this
     */
    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }
}

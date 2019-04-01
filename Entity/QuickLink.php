<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\QuickLinkRepository")
 */
class QuickLink extends AbstractTaxonomy
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $href;

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(string $href): self
    {
        $this->href = $href;

        return $this;
    }
}

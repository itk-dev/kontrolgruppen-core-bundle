<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CVR;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Client", inversedBy="companies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\Column(type="boolean")
     */
    private $highlighted;

    /**
     * Get Id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get CVR.
     *
     * @return string|null
     */
    public function getCVR(): ?string
    {
        return $this->CVR;
    }

    /**
     * Set CVR.
     *
     * @param string $cvr
     *
     * @return $this
     */
    public function setCVR(string $cvr): self
    {
        $this->CVR = $cvr;

        return $this;
    }

    /**
     * Get Client.
     *
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Set Client.
     *
     * @param Client|null $client
     *
     * @return $this
     */
    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Is highlighted.
     *
     * @return bool|null
     */
    public function isHighlighted(): ?bool
    {
        return $this->highlighted;
    }

    /**
     * Set highlighted.
     *
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

<?php

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessRepository")
 */
class Process
{
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\User", inversedBy="processes")
     */
    private $caseWorker;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caseNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientCPR;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Channel", inversedBy="processes")
     */
    private $channel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaseWorker(): ?User
    {
        return $this->caseWorker;
    }

    public function setCaseWorker(?User $caseWorker): self
    {
        $this->caseWorker = $caseWorker;

        return $this;
    }

    public function getCaseNumber(): ?string
    {
        return $this->caseNumber;
    }

    public function setCaseNumber(string $caseNumber): self
    {
        $this->caseNumber = $caseNumber;

        return $this;
    }

    public function getClientCPR(): ?string
    {
        return $this->clientCPR;
    }

    public function setClientCPR(string $clientCPR): self
    {
        $this->clientCPR = $clientCPR;

        return $this;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }
}

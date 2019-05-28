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
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\JournalEntryRepository")
 * @Gedmo\Loggable()
 */
class JournalEntry extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned()
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="journalEntries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    /**
     * @ORM\Column(name="type", type="JournalEntryEnumType", nullable=false)
     * @DoctrineAssert\Enum(entity="Kontrolgruppen\CoreBundle\DBAL\Types\JournalEntryEnumType")
     * @Gedmo\Versioned()
     */
    protected $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(?Process $process): self
    {
        $this->process = $process;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): void
    {
        $this->type = $type;
    }
}

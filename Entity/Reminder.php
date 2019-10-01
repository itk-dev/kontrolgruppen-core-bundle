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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ReminderRepository")
 * @Gedmo\Loggable()
 */
class Reminder extends AbstractEntity implements ProcessLoggableInterface
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $message;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Versioned()
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", inversedBy="reminders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $process;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Gedmo\Versioned()
     */
    private $finished;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(?bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }
}

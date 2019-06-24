<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kontrolgruppen\CoreBundle\Validator as KontrolgruppenAssert;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessRepository")
 * @Gedmo\Loggable()
 */
class Process extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\User", inversedBy="processes")
     */
    private $caseWorker;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned()
     */
    private $caseNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @KontrolgruppenAssert\CPR
     * @Gedmo\Versioned()
     */
    private $clientCPR;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Channel", inversedBy="processes")
     * @Gedmo\Versioned()
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service", inversedBy="processes")
     * @Gedmo\Versioned()
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", inversedBy="processes")
     * @ORM\JoinColumn(name="process_type_id", referencedColumnName="id", nullable=false)
     * @Gedmo\Versioned()
     */
    private $processType;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus", inversedBy="processes")
     * @Gedmo\Versioned()
     */
    private $processStatus;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Reminder", mappedBy="process", orphanRemoval=true)
     */
    private $reminders;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\JournalEntry", mappedBy="process", orphanRemoval=true)
     */
    private $journalEntries;

    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Client", mappedBy="process", cascade={"persist", "remove"})
     */
    private $client;

    /**
     * @ORM\OneToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Conclusion", mappedBy="process", cascade={"persist", "remove"})
     */
    private $conclusion;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\EconomyEntry", mappedBy="process", orphanRemoval=true)
     */
    private $economyEntries;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry", mappedBy="process", cascade={"persist", "remove"})
     */
    private $logEntries;

    public function __construct()
    {
        $this->reminders = new ArrayCollection();
        $this->journalEntries = new ArrayCollection();
        $this->economyEntries = new ArrayCollection();
        $this->logEntries = new ArrayCollection();
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getProcessType(): ?ProcessType
    {
        return $this->processType;
    }

    public function setProcessType(?ProcessType $processType): self
    {
        $this->processType = $processType;

        return $this;
    }

    public function getProcessStatus(): ?ProcessStatus
    {
        return $this->processStatus;
    }

    public function setProcessStatus(?ProcessStatus $processStatus): self
    {
        $this->processStatus = $processStatus;

        return $this;
    }

    /**
     * @return Collection|Reminder[]
     */
    public function getReminders(): Collection
    {
        return $this->reminders;
    }

    public function addReminder(Reminder $reminder): self
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders[] = $reminder;
            $reminder->setProcess($this);
        }

        return $this;
    }

    public function removeReminder(Reminder $reminder): self
    {
        if ($this->reminders->contains($reminder)) {
            $this->reminders->removeElement($reminder);
            // set the owning side to null (unless already changed)
            if ($reminder->getProcess() === $this) {
                $reminder->setProcess(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JournalEntry[]
     */
    public function getJournalEntries(): Collection
    {
        return $this->journalEntries;
    }

    public function addJournalEntry(JournalEntry $journalEntry): self
    {
        if (!$this->journalEntries->contains($journalEntry)) {
            $this->journalEntries[] = $journalEntry;
            $journalEntry->setProcess($this);
        }

        return $this;
    }

    public function removeJournalEntry(JournalEntry $journalEntry): self
    {
        if ($this->journalEntries->contains($journalEntry)) {
            $this->journalEntries->removeElement($journalEntry);
            // set the owning side to null (unless already changed)
            if ($journalEntry->getProcess() === $this) {
                $journalEntry->setProcess(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        // set the owning side of the relation if necessary
        if ($this !== $client->getProcess()) {
            $client->setProcess($this);
        }

        return $this;
    }

    public function getConclusion(): ?Conclusion
    {
        return $this->conclusion;
    }

    public function setConclusion(?Conclusion $conclusion): self
    {
        $this->conclusion = $conclusion;

        // set (or unset) the owning side of the relation if necessary
        $newProcess = null === $conclusion ? null : $this;
        if ($newProcess !== $conclusion->getProcess()) {
            $conclusion->setProcess($newProcess);
        }

        return $this;
    }

    /**
     * @return Collection|EconomyEntry[]
     */
    public function getEconomyEntries(): Collection
    {
        return $this->economyEntries;
    }

    public function addEconomyEntry(EconomyEntry $economyEntry): self
    {
        if (!$this->economyEntries->contains($economyEntry)) {
            $this->economyEntries[] = $economyEntry;
            $economyEntry->setProcess($this);
        }

        return $this;
    }

    public function removeEconomyEntry(EconomyEntry $economyEntry): self
    {
        if ($this->economyEntries->contains($economyEntry)) {
            $this->economyEntries->removeElement($economyEntry);
            // set the owning side to null (unless already changed)
            if ($economyEntry->getProcess() === $this) {
                $economyEntry->setProcess(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProcessLogEntry[]
     */
    public function getLogEntries(): Collection
    {
        return $this->logEntries;
    }

    public function addLogEntry(ProcessLogEntry $logEntry): self
    {
        if (!$this->logEntries->contains($logEntry)) {
            $this->logEntries[] = $logEntry;
            $logEntry->setProcess($this);
        }

        return $this;
    }

    public function removeLogEntry(ProcessLogEntry $logEntry): self
    {
        if ($this->logEntries->contains($logEntry)) {
            $this->logEntries->removeElement($logEntry);
            // set the owning side to null (unless already changed)
            if ($logEntry->getProcess() === $this) {
                $logEntry->setProcess(null);
            }
        }

        return $this;
    }
}

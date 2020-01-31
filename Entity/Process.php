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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\ProcessRepository")
 *
 * @Gedmo\Loggable()
 */
class Process extends AbstractEntity
{
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $completedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\User", inversedBy="processes")
     *
     * @Gedmo\Versioned()
     */
    private $caseWorker;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Gedmo\Versioned()
     */
    private $caseNumber;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @KontrolgruppenAssert\CPR
     *
     * @Gedmo\Versioned()
     */
    private $clientCPR;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Channel", inversedBy="processes")
     *
     * @Gedmo\Versioned()
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Reason", inversedBy="processes")
     *
     * @Gedmo\Versioned()
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\Service", inversedBy="processes")
     *
     * @Gedmo\Versioned()
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessType", inversedBy="processes")
     * @ORM\JoinColumn(name="process_type_id", referencedColumnName="id", nullable=false)
     *
     * @Gedmo\Versioned()
     */
    private $processType;

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessStatus", inversedBy="processes")
     *
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
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry", mappedBy="process", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $logEntries;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Gedmo\Versioned()
     */
    private $policeReport;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $courtDecision;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lockedNetValue;

    /**
     * @var bool
     */
    private $visitedByCaseWorker = false;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\LockedNetValue", mappedBy="process")
     */
    private $lockedNetValues;

    /**
     * Process constructor.
     */
    public function __construct()
    {
        $this->reminders = new ArrayCollection();
        $this->journalEntries = new ArrayCollection();
        $this->economyEntries = new ArrayCollection();
        $this->logEntries = new ArrayCollection();
        $this->lockedNetValues = new ArrayCollection();
    }

    /**
     * @return \DateTime|null
     */
    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    /**
     * @param \DateTime|null $completedAt
     *
     * @return Process
     */
    public function setCompletedAt(?\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCaseWorker(): ?User
    {
        return $this->caseWorker;
    }

    /**
     * @param User|null $caseWorker
     *
     * @return Process
     */
    public function setCaseWorker(?User $caseWorker): self
    {
        $this->caseWorker = $caseWorker;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCaseNumber(): ?string
    {
        return $this->caseNumber;
    }

    /**
     * @param string $caseNumber
     *
     * @return Process
     */
    public function setCaseNumber(string $caseNumber): self
    {
        $this->caseNumber = $caseNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientCPR(): ?string
    {
        return $this->clientCPR;
    }

    /**
     * @param string $clientCPR
     *
     * @return Process
     */
    public function setClientCPR(string $clientCPR): self
    {
        $this->clientCPR = $clientCPR;

        return $this;
    }

    /**
     * @return Channel|null
     */
    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    /**
     * @param Channel|null $channel
     *
     * @return Process
     */
    public function setChannel(?Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return Reason|null
     */
    public function getReason(): ?Reason
    {
        return $this->reason;
    }

    /**
     * @param Reason|null $reason
     *
     * @return Process
     */
    public function setReason(?Reason $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service|null $service
     *
     * @return Process
     */
    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return ProcessType|null
     */
    public function getProcessType(): ?ProcessType
    {
        return $this->processType;
    }

    /**
     * @param ProcessType|null $processType
     *
     * @return Process
     */
    public function setProcessType(?ProcessType $processType): self
    {
        $this->processType = $processType;

        return $this;
    }

    /**
     * @return ProcessStatus|null
     */
    public function getProcessStatus(): ?ProcessStatus
    {
        return $this->processStatus;
    }

    /**
     * @param ProcessStatus|null $processStatus
     *
     * @return Process
     */
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

    /**
     * @param Reminder $reminder
     *
     * @return Process
     */
    public function addReminder(Reminder $reminder): self
    {
        if (!$this->reminders->contains($reminder)) {
            $this->reminders[] = $reminder;
            $reminder->setProcess($this);
        }

        return $this;
    }

    /**
     * @param Reminder $reminder
     *
     * @return Process
     */
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

    /**
     * @param JournalEntry $journalEntry
     *
     * @return Process
     */
    public function addJournalEntry(JournalEntry $journalEntry): self
    {
        if (!$this->journalEntries->contains($journalEntry)) {
            $this->journalEntries[] = $journalEntry;
            $journalEntry->setProcess($this);
        }

        return $this;
    }

    /**
     * @param JournalEntry $journalEntry
     *
     * @return Process
     */
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

    /**
     * @return Client|null
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     *
     * @return Process
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        // set the owning side of the relation if necessary
        if ($this !== $client->getProcess()) {
            $client->setProcess($this);
        }

        return $this;
    }

    /**
     * @return Conclusion|null
     */
    public function getConclusion(): ?Conclusion
    {
        return $this->conclusion;
    }

    /**
     * @param Conclusion|null $conclusion
     *
     * @return Process
     */
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

    /**
     * @param EconomyEntry $economyEntry
     *
     * @return Process
     */
    public function addEconomyEntry(EconomyEntry $economyEntry): self
    {
        if (!$this->economyEntries->contains($economyEntry)) {
            $this->economyEntries[] = $economyEntry;
            $economyEntry->setProcess($this);
        }

        return $this;
    }

    /**
     * @param EconomyEntry $economyEntry
     *
     * @return Process
     */
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

    /**
     * @param ProcessLogEntry $logEntry
     *
     * @return Process
     */
    public function addLogEntry(ProcessLogEntry $logEntry): self
    {
        if (!$this->logEntries->contains($logEntry)) {
            $this->logEntries[] = $logEntry;
            $logEntry->setProcess($this);
        }

        return $this;
    }

    /**
     * @param ProcessLogEntry $logEntry
     *
     * @return Process
     */
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

    /**
     * @return bool|null
     */
    public function getPoliceReport(): ?bool
    {
        return $this->policeReport;
    }

    /**
     * @param bool|null $policeReport
     *
     * @return Process
     */
    public function setPoliceReport(?bool $policeReport): self
    {
        $this->policeReport = $policeReport;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCourtDecision(): ?bool
    {
        return $this->courtDecision;
    }

    /**
     * @param bool|null $courtDecision
     *
     * @return Process
     */
    public function setCourtDecision(?bool $courtDecision): self
    {
        $this->courtDecision = $courtDecision;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLockedNetValue(): ?float
    {
        return $this->lockedNetValue;
    }

    /**
     * @param float|null $lockedNetValue
     *
     * @return Process
     */
    public function setLockedNetValue(?float $lockedNetValue): self
    {
        $this->lockedNetValue = $lockedNetValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVisitedByCaseWorker(): bool
    {
        return $this->visitedByCaseWorker;
    }

    /**
     * @param bool $visited
     */
    public function setVisitedByCaseWorker(bool $visited)
    {
        $this->visitedByCaseWorker = $visited;
    }

    /**
     * @return Collection|LockedNetValue[]
     */
    public function getLockedNetValues(): Collection
    {
        return $this->lockedNetValues;
    }

    /**
     * @param LockedNetValue $lockedNetValue
     *
     * @return Process
     */
    public function addLockedNetValue(LockedNetValue $lockedNetValue): self
    {
        if (!$this->lockedNetValues->contains($lockedNetValue)) {
            $this->lockedNetValues[] = $lockedNetValue;
            $lockedNetValue->setProcess($this);
        }

        return $this;
    }

    /**
     * @param LockedNetValue $lockedNetValue
     *
     * @return Process
     */
    public function removeLockedNetValue(LockedNetValue $lockedNetValue): self
    {
        if ($this->lockedNetValues->contains($lockedNetValue)) {
            $this->lockedNetValues->removeElement($lockedNetValue);
            // set the owning side to null (unless already changed)
            if ($lockedNetValue->getProcess() === $this) {
                $lockedNetValue->setProcess(null);
            }
        }

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     * @param                           $payload
     */
    public function validateCourtDecision(ExecutionContextInterface $context, $payload)
    {
        if (true === $this->getPoliceReport() && null === $this->getCourtDecision()) {
            $context->buildViolation('Court decision is required when police report is true')
                ->atPath('courtDecision')
                ->addViolation();
        }
    }
}

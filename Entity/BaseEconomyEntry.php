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
class BaseEconomyEntry extends EconomyEntry
{
    /**
     * @ORM\Column(type="integer")
     *
     * @Gedmo\Versioned()
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Versioned()
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="\Kontrolgruppen\CoreBundle\Entity\Account", inversedBy="baseEconomyEntries")
     */
    private $account;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getAccountNumber(): ?int
    {
        return $this->accountNumber;
    }

    /**
     * @param int $accountNumber
     *
     * @return BaseEconomyEntry
     */
    public function setAccountNumber(int $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface $date
     *
     * @return BaseEconomyEntry
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Account|null
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @param Account|null $account
     *
     * @return BaseEconomyEntry
     */
    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }
}

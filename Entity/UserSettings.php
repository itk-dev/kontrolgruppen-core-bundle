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
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\UserSettingsRepository")
 */
class UserSettings
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
    private $settingsKey;

    /**
     * @ORM\Column(type="json")
     */
    private $settingsValue = [];

    /**
     * @ORM\ManyToOne(targetEntity="Kontrolgruppen\CoreBundle\Entity\User", inversedBy="userSettings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSettingsKey(): ?string
    {
        return $this->settingsKey;
    }

    public function setSettingsKey(string $settingsKey): self
    {
        $this->settingsKey = $settingsKey;

        return $this;
    }

    public function getSettingsValue(): ?array
    {
        return $this->settingsValue;
    }

    public function setSettingsValue(array $settingsValue): self
    {
        $this->settingsValue = $settingsValue;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

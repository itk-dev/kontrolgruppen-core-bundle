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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getSettingsKey(): ?string
    {
        return $this->settingsKey;
    }

    /**
     * @param string $settingsKey
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\UserSettings
     */
    public function setSettingsKey(string $settingsKey): self
    {
        $this->settingsKey = $settingsKey;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getSettingsValue(): ?array
    {
        return $this->settingsValue;
    }

    /**
     * @param array $settingsValue
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\UserSettings
     */
    public function setSettingsValue(array $settingsValue): self
    {
        $this->settingsValue = $settingsValue;

        return $this;
    }

    /**
     * @return \Kontrolgruppen\CoreBundle\Entity\User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param \Kontrolgruppen\CoreBundle\Entity\User|null $user
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\UserSettings
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

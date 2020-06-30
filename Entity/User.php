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
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="Kontrolgruppen\CoreBundle\Repository\UserRepository")
 *
 * @Gedmo\Loggable()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\Process", mappedBy="caseWorker")
     */
    private $processes;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cliLoginToken;

    /**
     * @ORM\OneToMany(targetEntity="Kontrolgruppen\CoreBundle\Entity\UserSettings", mappedBy="user", orphanRemoval=true)
     */
    private $userSettings;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $name;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->userSettings = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Process[]
     */
    public function getProcesses(): Collection
    {
        return $this->processes ?? new ArrayCollection();
    }

    /**
     * @param Process $process
     *
     * @return User
     */
    public function addProcess(Process $process): self
    {
        if (!$this->processes->contains($process)) {
            $this->processes[] = $process;
            $process->setCaseWorker($this);
        }

        return $this;
    }

    /**
     * @param Process $process
     *
     * @return User
     */
    public function removeProcess(Process $process): self
    {
        if ($this->processes->contains($process)) {
            $this->processes->removeElement($process);
            // set the owning side to null (unless already changed)
            if ($process->getCaseWorker() === $this) {
                $process->setCaseWorker(null);
            }
        }

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getCliLoginToken()
    {
        return $this->cliLoginToken;
    }

    /**
     * @param mixed $cliLoginToken
     *
     * @return User
     */
    public function setCliLoginToken($cliLoginToken)
    {
        $this->cliLoginToken = $cliLoginToken;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * @return Collection|UserSettings[]
     */
    public function getUserSettings(): Collection
    {
        return $this->userSettings;
    }

    /**
     * @param \Kontrolgruppen\CoreBundle\Entity\UserSettings $userSetting
     *
     * @return User
     */
    public function addUserSetting(UserSettings $userSetting): self
    {
        if (!$this->userSettings->contains($userSetting)) {
            $this->userSettings[] = $userSetting;
            $userSetting->setUser($this);
        }

        return $this;
    }

    /**
     * @param \Kontrolgruppen\CoreBundle\Entity\UserSettings $userSetting
     *
     * @return User
     */
    public function removeUserSetting(UserSettings $userSetting): self
    {
        if ($this->userSettings->contains($userSetting)) {
            $this->userSettings->removeElement($userSetting);
            // set the owning side to null (unless already changed)
            if ($userSetting->getUser() === $this) {
                $userSetting->setUser(null);
            }
        }

        return $this;
    }
}

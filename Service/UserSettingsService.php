<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\User;
use Kontrolgruppen\CoreBundle\Entity\UserSettings;
use Kontrolgruppen\CoreBundle\Repository\UserSettingsRepository;

/**
 * Class UserSettingsService.
 *
 * Provides functions for getting/setting a user's settings.
 */
class UserSettingsService
{
    private $settingsRepository;
    private $entityManager;

    /**
     * UserSettingsService constructor.
     *
     * @param \Kontrolgruppen\CoreBundle\Repository\UserSettingsRepository $settingsRepository
     *   The UserSettings repository
     * @param \Doctrine\ORM\EntityManagerInterface                         $entityManager
     *   The entity manager
     */
    public function __construct(UserSettingsRepository $settingsRepository, EntityManagerInterface $entityManager)
    {
        $this->settingsRepository = $settingsRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Get a settings value for a given user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *   The user
     * @param string                                 $settingsKey
     *   The settings key
     *
     * @return \Kontrolgruppen\CoreBundle\Entity\UserSettings|null
     */
    public function getSettings(User $user, string $settingsKey)
    {
        return $this->settingsRepository->findOneBy([
            'user' => $user,
            'settingsKey' => $settingsKey,
        ]);
    }

    /**
     * Set a settings value for a given user.
     *
     * @param \Kontrolgruppen\CoreBundle\Entity\User $user
     *   The user
     * @param string                                 $settingsKey
     *   The settings key
     * @param array                                  $settingValue
     *   The setting value
     */
    public function setSettings(User $user, string $settingsKey, array $settingValue)
    {
        $settings = $this->settingsRepository->findOneBy([
            'user' => $user,
            'settingsKey' => $settingsKey,
        ]);

        if (null === $settings) {
            $settings = new UserSettings();
            $settings->setUser($user);
            $settings->setSettingsKey($settingsKey);
            $this->entityManager->persist($settings);
        }

        $settings->setSettingsValue($settingValue);

        $this->entityManager->flush();
    }
}

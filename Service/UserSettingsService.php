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
use Kontrolgruppen\CoreBundle\Entity\UserSettings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserSettingsService.
 */
class UserSettingsService
{
    private $entityManager;

    /**
     * UserSettingsService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an array with information about user preferred sorting information structured like this:.
     * [
     *     'sort' => 'key', // e.g. caseWorker.id
     *     'direction' => 'direction' // desc or asc
     * ]
     * If no user preferred sorting exists and the request contains information about sorting the method will
     * persist the request information as user preferred sorting. The return value will then be null as there aren't
     * any new information about sorting available.
     * If both the request and the data storage contains information about sorting and they differ, the information
     * from the request will override the stored information and be persisted. The return value will then be null.
     *
     * @param Request      $request
     * @param UserSettings $userSettings
     *
     * @return array|null
     */
    public function handleProcessIndexRequest(Request $request, UserSettings $userSettings): ?array
    {
        $sort = $request->query->get('sort');
        $direction = $request->query->get('direction');

        // Check if there is sort and direction persisted
        // If not we may want to save user selected sort and direction
        if (empty($userSettings->getProcessIndexSort())) {
            // Nothing selected so no need to persist anything
            if (empty($sort) || empty($direction)) {
                return null;
            }

            $userSettings->setProcessIndexSort($sort, $direction);

            $this->entityManager->persist($userSettings);
            $this->entityManager->flush();

            return null;
        }

        $persistedSortAndDirection = $userSettings->getProcessIndexSort();

        // If sort and direction is not present in the query, we return the persisted sort and direction
        if (empty($sort) || empty($direction)) {
            return $persistedSortAndDirection;
        }

        // If sort and direction is present in the query and they differ from the persisted sort and direction, we will
        // save the new selection of sort and direction
        if ($sort !== $persistedSortAndDirection['sort'] || $direction !== $persistedSortAndDirection['direction']) {
            $userSettings->setProcessIndexSort($sort, $direction);

            $this->entityManager->persist($userSettings);
            $this->entityManager->flush();

            return null;
        }

        return null;
    }
}

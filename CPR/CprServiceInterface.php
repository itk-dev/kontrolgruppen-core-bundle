<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use Kontrolgruppen\CoreBundle\Entity\Client;

interface CprServiceInterface
{
    /**
     * @param int $cpr
     *
     * @return array
     *
     * @throws CprException
     */
    public function find(int $cpr): array;

    /**
     * Populates client with information from the CPR service. If no data is found via the service the client
     * object is returned without being changed.
     *
     * @param int    $cpr
     * @param Client $client
     *
     * @return Client
     *
     * @throws CprException
     */
    public function populateClient(int $cpr, Client $client): Client;

    /**
     * @param int    $cpr
     * @param Client $client
     *
     * @return bool
     *
     * @throws CprException
     */
    public function isNewClientInfoAvailable(int $cpr, Client $client): bool;
}

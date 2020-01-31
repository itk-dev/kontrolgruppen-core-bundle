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

/**
 * Interface CprServiceInterface.
 */
interface CprServiceInterface
{
    /**
     * @param Cpr $cpr
     *
     * @return CprServiceResultInterface
     *
     * @throws CprException
     */
    public function find(Cpr $cpr): CprServiceResultInterface;

    /**
     * Populates client with information from the CPR service. If no data is found via the service the client
     * object is returned without being changed.
     *
     * @param Cpr    $cpr
     * @param Client $client
     *
     * @return Client
     *
     * @throws CprException
     */
    public function populateClient(Cpr $cpr, Client $client): Client;

    /**
     * @param Cpr    $cpr
     * @param Client $client
     *
     * @return bool
     *
     * @throws CprException
     */
    public function isNewClientInfoAvailable(Cpr $cpr, Client $client): bool;
}

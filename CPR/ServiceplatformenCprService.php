<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use ItkDev\Serviceplatformen\Service\PersonBaseDataExtendedService;

/**
 * Class ServiceplatformenCprService.
 */
class ServiceplatformenCprService extends AbstractCprService implements CprServiceInterface
{
    private $service;

    /**
     * ServiceplatformenCprService constructor.
     *
     * @param PersonBaseDataExtendedService $service
     */
    public function __construct(PersonBaseDataExtendedService $service)
    {
        $this->service = $service;
    }

    /**
     * Fetches the person data associated with the CPR.
     *
     * @param Cpr $cpr
     *
     * @return CprServiceResultInterface
     */
    public function find(Cpr $cpr): CprServiceResultInterface
    {
        $response = $this->service->personLookup($cpr);

        return new ServiceplatformenCprServiceResult($response);
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use ItkDev\Serviceplatformen\Service\Exception\ServiceException;
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
     * {@inheritdoc}
     */
    public function find(Cpr $cpr): CprServiceResultInterface
    {
        try {
            $response = $this->service->personLookup($cpr);
        } catch (ServiceException $e) {
            throw new CprException($e->getMessage(), $e->getCode(), $e);
        }

        return new ServiceplatformenCprServiceResult($response);
    }
}

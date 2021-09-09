<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CVR;

use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class ServiceplatformenCvrServiceResult.
 */
class ServiceplatformenCvrServiceResult implements CvrServiceResultInterface
{
    private $response;
    private $propertyAccessor;

    /**
     * ServiceplatformenCvrServiceResult constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->getProperty('persondata.navn.fornavn');
    }

    /**
     * Get middle name.
     *
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->propertyAccessor->isReadable($this->response, 'persondata.navn.mellemnavn')
            ? $this->propertyAccessor->getValue($this->response, 'persondata.navn.mellemnavn')
            : null
        ;
    }

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->getProperty('persondata.navn.efternavn');
    }

    /**
     * Get street name.
     *
     * @return string
     */
    public function getStreetName(): string
    {
        return $this->getProperty('adresse.aktuelAdresse.vejnavn');
    }

    /**
     * Get house number.
     *
     * @return string
     */
    public function getHouseNumber(): ?string
    {
        return $this->getProperty('adresse.aktuelAdresse.husnummer');
    }

    /**
     * Get floor.
     *
     * @return string|null
     */
    public function getFloor(): ?string
    {
        return $this->propertyAccessor->isReadable($this->response, 'adresse.aktuelAdresse.etage')
            ? $this->propertyAccessor->getValue($this->response, 'adresse.aktuelAdresse.etage')
            : null
        ;
    }

    /**
     * Get side.
     *
     * @return string|null
     */
    public function getSide(): ?string
    {
        return $this->propertyAccessor->isReadable($this->response, 'adresse.aktuelAdresse.sidedoer')
            ? $this->propertyAccessor->getValue($this->response, 'adresse.aktuelAdresse.sidedoer')
            : null
        ;
    }

    /**
     * Get postal code.
     *
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->getProperty('adresse.aktuelAdresse.postnummer');
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity(): string
    {
        return $this->getProperty('adresse.aktuelAdresse.postdistrikt');
    }

    /**
     * Returns the value of the property if it exists otherwise it returns an empty string.
     *
     * @param string $property name of property
     *
     * @return string
     */
    private function getProperty(string $property): string
    {
        return $this->propertyAccessor->isReadable($this->response, $property)
            ? $this->propertyAccessor->getValue($this->response, $property)
            : ''
        ;
    }
}

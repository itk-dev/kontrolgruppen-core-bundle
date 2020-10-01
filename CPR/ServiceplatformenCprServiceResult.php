<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

/**
 * Class ServiceplatformenCprServiceResult.
 */
class ServiceplatformenCprServiceResult implements CprServiceResultInterface
{
    private $response;

    /**
     * ServiceplatformenCprServiceResult constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * Get first name.
     */
    public function getFirstName(): string
    {
        return $this->response
            ->persondata
            ->navn
            ->fornavn;
    }

    /**
     * Get middle name.
     */
    public function getMiddleName(): ?string
    {
        $navn = $this->response->persondata->navn;

        if (property_exists($navn, 'mellemnavn')) {
            return $navn->mellemnavn;
        }

        return null;
    }

    /**
     * Get last name.
     */
    public function getLastName(): string
    {
        return $this->response
            ->persondata
            ->navn
            ->efternavn;
    }

    /**
     * Get street name.
     */
    public function getStreetName(): string
    {
        return $this->response
            ->adresse
            ->aktuelAdresse
            ->vejnavn;
    }

    /**
     * Get house number.
     *
     * @return string
     */
    public function getHouseNumber(): ?string
    {
        return $this->response
            ->adresse
            ->aktuelAdresse
            ->husnummer;
    }

    /**
     * Get floor.
     */
    public function getFloor(): ?string
    {
        $aktuelAdresse = $this->response->adresse->aktuelAdresse;

        if (property_exists($aktuelAdresse, 'etage')) {
            return $aktuelAdresse->etage;
        }

        return null;
    }

    /**
     * Get side.
     */
    public function getSide(): ?string
    {
        $aktuelAdresse = $this->response->adresse->aktuelAdresse;

        if (property_exists($aktuelAdresse, 'sidedoer')) {
            return $aktuelAdresse->sidedoer;
        }

        return null;
    }

    /**
     * Get postal code.
     *
     * @return string|null
     */
    public function getPostalCode(): string
    {
        return $this->response
            ->adresse
            ->aktuelAdresse
            ->postnummer;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity(): string
    {
        return $this->response
            ->adresse
            ->aktuelAdresse
            ->postdistrikt;
    }
}

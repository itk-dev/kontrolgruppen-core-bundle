<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

class FaellesSQLCprServiceResult implements CprServiceResult
{
    private $serviceResult;

    public function __construct(array $serviceResult)
    {
        $this->setServiceResult($serviceResult);
    }

    private function setServiceResult(array $serviceResult)
    {
        $schema = [
            'Fornavn',
            'Efternavn',
            'Vejnavn',
            'HusNr',
            'Etage',
            'Side',
            'Postnummer',
            'Bynavn',
        ];

        $missingKeys = [];
        foreach ($schema as $key) {
            if (!\array_key_exists($key, $serviceResult)) {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            $message = sprintf(
                'Result does not have expected key(s) present: %s',
                implode(', ', $missingKeys)
            );
            throw new \InvalidArgumentException($message);
        }

        $this->serviceResult = $serviceResult;
    }

    public function getFirstName(): ?string
    {
        return $this->serviceResult['Fornavn'];
    }

    public function getLastName(): ?string
    {
        return $this->serviceResult['Efternavn'];
    }

    public function getStreetName(): ?string
    {
        return $this->serviceResult['Vejnavn'];
    }

    public function getHouseNumber(): ?string
    {
        return $this->serviceResult['HusNr'];
    }

    public function getFloor(): ?string
    {
        return $this->serviceResult['Etage'];
    }

    public function getSide(): ?string
    {
        return $this->serviceResult['Side'];
    }

    public function getPostalCode(): ?string
    {
        return $this->serviceResult['Postnummer'];
    }

    public function getCity(): ?string
    {
        return $this->serviceResult['Bynavn'];
    }
}

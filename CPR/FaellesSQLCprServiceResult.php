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
        // The schema array describes which keys are expected to be present in the result and whether values has to be
        // to be present for the key. True means the value has to be present.
        $schema = [
            'Fornavn' => true,
            'Efternavn' => true,
            'Vejnavn' => true,
            'HusNr' => true,
            'Etage' => false,
            'Side' => false,
            'Postnummer' => true,
            'Postdistrikt' => true,
        ];

        $missingKeys = [];
        $missingRequiredValues = [];
        foreach ($schema as $key => $required) {
            if (!\array_key_exists($key, $serviceResult)) {
                $missingKeys[] = $key;
            } elseif ($required && empty($serviceResult[$key])) {
                $missingRequiredValues[] = $key;
            }
        }

        $errors = [];
        if (!empty($missingKeys)) {
            $errors[] = sprintf(
                'Result does not have expected key(s) present: %s',
                implode(', ', $missingKeys)
            );
        }

        if (!empty($missingRequiredValues)) {
            $errors[] = sprintf(
                'Result is missing values for following required key(s): %s',
                implode(', ', $missingRequiredValues)
            );
        }

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }

        $this->serviceResult = $serviceResult;
    }

    public function getFirstName(): string
    {
        return $this->serviceResult['Fornavn'];
    }

    public function getLastName(): string
    {
        return $this->serviceResult['Efternavn'];
    }

    public function getStreetName(): string
    {
        return $this->serviceResult['Vejnavn'];
    }

    public function getHouseNumber(): string
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

    public function getPostalCode(): string
    {
        return $this->serviceResult['Postnummer'];
    }

    public function getCity(): string
    {
        return $this->serviceResult['Postdistrikt'];
    }
}

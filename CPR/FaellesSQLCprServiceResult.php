<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FaellesSQLCprServiceResult implements CprServiceResult
{
    private $serviceResult;

    public function __construct(array $serviceResult)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        $this->serviceResult = $optionsResolver->resolve($serviceResult);
    }

    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefined([
            'CPR',
            'Bynavn',
            'Mellemnavn',
            'PostnummerOgBy',
            'Adresseringsadresse',
            'Adresseringsnavn',
        ]);

        $optionsResolver->setRequired([
            'Fornavn',
            'Efternavn',
            'Vejnavn',
            'HusNr',
            'Etage',
            'Side',
            'Postnummer',
            'Postdistrikt',
        ]);

        $optionsResolver->setAllowedTypes('Fornavn', 'string');
        $optionsResolver->setAllowedTypes('Efternavn', 'string');
        $optionsResolver->setAllowedTypes('Vejnavn', 'string');
        $optionsResolver->setAllowedTypes('HusNr', ['null', 'string']);
        $optionsResolver->setAllowedTypes('Etage', ['null', 'string']);
        $optionsResolver->setAllowedTypes('Side', ['null', 'string']);
        $optionsResolver->setAllowedTypes('Postnummer', 'string');
        $optionsResolver->setAllowedTypes('Postdistrikt', 'string');
    }

    public function getFirstName(): string
    {
        return $this->serviceResult['Fornavn'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMiddleName(): ?string
    {
        return $this->serviceResult['Mellemnavn'];
    }

    public function getLastName(): string
    {
        return $this->serviceResult['Efternavn'];
    }

    public function getStreetName(): string
    {
        return $this->serviceResult['Vejnavn'];
    }

    /**
     * {@inheritdoc}
     */
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

    public function getPostalCode(): string
    {
        return $this->serviceResult['Postnummer'];
    }

    public function getCity(): string
    {
        return $this->serviceResult['Postdistrikt'];
    }
}

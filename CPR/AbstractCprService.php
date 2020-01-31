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
 * Class AbstractCprService.
 */
abstract class AbstractCprService implements CprServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function populateClient(Cpr $cpr, Client $client): Client
    {
        $result = $this->find($cpr);

        if (empty($result)) {
            return $client;
        }

        $client->setFirstName($result->getFirstName());
        $client->setLastName($result->getLastName());
        $client->setAddress($this->generateAddressString($result));
        $client->setPostalCode($result->getPostalCode());
        $client->setCity($result->getCity());

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function isNewClientInfoAvailable(Cpr $cpr, Client $client): bool
    {
        $result = $this->find($cpr);

        if (empty($result)) {
            return false;
        }

        $comparisons = [
            $client->getFirstName() => $result->getFirstName(),
            $client->getLastName() => $result->getLastName(),
            $client->getAddress() => $this->generateAddressString($result),
            $client->getPostalCode() => $result->getPostalCode(),
            $client->getCity() => $result->getCity(),
        ];

        foreach ($comparisons as $key => $value) {
            if (strtolower(trim($key)) !== strtolower(trim($value))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param CprServiceResult $result
     *
     * @return string
     */
    private function generateAddressString(CprServiceResult $result): string
    {
        $address = $result->getStreetName();
        $address .= ' '.$result->getHouseNumber();

        $address .= (!empty($result->getFloor())) ? ' '.$result->getFloor() : '';
        $address .= (!empty($result->getSide())) ? ' '.$result->getSide() : '';

        return $address;
    }
}

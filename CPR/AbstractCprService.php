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

        $firstName = $result->getFirstName();
        if (null !== $result->getMiddleName()) {
            $firstName .= ' '.$result->getMiddleName();
        }

        $client->setFirstName($firstName);
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

        $firstName = $result->getFirstName();
        if (null !== $result->getMiddleName()) {
            $firstName .= ' '.$result->getMiddleName();
        }

        $comparisons = [
            $client->getFirstName() => $firstName,
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
     * Generate address string.
     *
     * @param CprServiceResultInterface $result
     *
     * @return string
     */
    private function generateAddressString(CprServiceResultInterface $result): string
    {
        $address = $result->getStreetName();

        $address .= null !== $result->getHouseNumber()
            ? ' '.$result->getHouseNumber()
            : ''
        ;

        $address .= null !== $result->getFloor()
            ? ' '.$result->getFloor()
            : ''
        ;

        $address .= null !== $result->getSide()
            ? ' '.$result->getSide()
            : ''
        ;

        return $address;
    }
}

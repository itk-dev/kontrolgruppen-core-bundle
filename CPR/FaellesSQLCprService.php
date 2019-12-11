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
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FaellesSQLCprService implements CprServiceInterface
{
    private const CITIZEN_ENDPOINT = 'citizen';

    private $serviceUrl;
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient, $cprServiceUrl)
    {
        $this->serviceUrl = $cprServiceUrl;
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Cpr $cpr): array
    {
        $url = sprintf('%s/%s/%s', $this->serviceUrl, self::CITIZEN_ENDPOINT, $cpr);

        try {
            $response = $this->httpClient->request('GET', $url);

            return $response->toArray();
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            throw new CprException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function populateClient(Cpr $cpr, Client $client): Client
    {
        $cprResult = $this->find($cpr);

        if (!empty($cprResult)) {
            $client->setFirstName($cprResult['Fornavn']);
            $client->setLastName($cprResult['Efternavn']);
            $client->setAddress($this->generateAddressString($cprResult));
            $client->setPostalCode($cprResult['Postnummer']);
            $client->setCity($cprResult['Bynavn']);
        }

        return $client;
    }

    private function generateAddressString($cprResult): string
    {
        $address = $cprResult['Vejnavn'];
        $address .= ' '.$cprResult['Husnr'];

        $address .= (!empty($cprResult['Etage'])) ? ' '.$cprResult['Etage'] : '';
        $address .= (!empty($cprResult['Side'])) ? ' '.$cprResult['Side'] : '';

        return $address;
    }

    /**
     * {@inheritdoc}
     */
    public function isNewClientInfoAvailable(Cpr $cpr, Client $client): bool
    {
        $cprResult = $this->find($cpr);

        if (empty($cprResult)) {
            return false;
        }

        $comparisons = [
            $client->getFirstName() => $cprResult['Fornavn'],
            $client->getLastName() => $cprResult['Efternavn'],
            $client->getAddress() => $this->generateAddressString($cprResult),
            $client->getPostalCode() => $cprResult['Postnummer'],
            $client->getCity() => $cprResult['Bynavn'],
        ];

        foreach ($comparisons as $key => $value) {
            if (strtolower(trim($key)) !== strtolower(trim($value))) {
                return true;
            }
        }

        return false;
    }
}

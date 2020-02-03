<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class FaellesSQLCprService.
 */
class FaellesSQLCprService extends AbstractCprService implements CprServiceInterface
{
    private const CITIZEN_ENDPOINT = 'citizen';

    private $serviceUrl;
    private $httpClient;

    /**
     * FaellesSQLCprService constructor.
     *
     * @param HttpClientInterface $httpClient
     * @param                     $cprServiceUrl
     */
    public function __construct(HttpClientInterface $httpClient, $cprServiceUrl)
    {
        $this->serviceUrl = $cprServiceUrl;
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Cpr $cpr): CprServiceResultInterface
    {
        $url = sprintf('%s/%s/%s', $this->serviceUrl, self::CITIZEN_ENDPOINT, $cpr);

        try {
            $response = $this->httpClient->request('GET', $url, ['timeout' => 2]);

            return new FaellesSQLCprServiceResult($response->toArray());
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | \InvalidArgumentException $e) {
            throw new CprException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

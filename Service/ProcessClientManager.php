<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\CPR\Cpr;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class ProcessClientManager.
 */
class ProcessClientManager
{
    private $cprService;
    private $propertyAccessor;
    private $logger;

    /**
     * Constructor.
     *
     * @param CprServiceInterface       $cprService
     * @param PropertyAccessorInterface $propertyAccessor
     * @param LoggerInterface           $logger
     */
    public function __construct(CprServiceInterface $cprService, PropertyAccessorInterface $propertyAccessor, LoggerInterface $logger)
    {
        $this->cprService = $cprService;
        $this->propertyAccessor = $propertyAccessor;
        $this->logger = $logger;
    }

    /**
     * Create a process client.
     *
     * @param string $type
     *                           The client type ("company" or "person")
     * @param array  $properties
     *                           The client properties
     *
     * @return abstractProcessClient
     *                               The client
     *
     * @throws \Kontrolgruppen\CoreBundle\CPR\CprException
     */
    public function createClient(string $type, array $properties = []): AbstractProcessClient
    {
        switch ($type) {
            case 'company':
                $client = new ProcessClientCompany();
                if (isset($properties['cvr'])) {
//                    $this->cvrService->populateClient(new Cvr($properties['cvr']), $client);
                }
                break;

            case 'person':
                $client = new ProcessClientPerson();
                if (isset($properties['cpr'])) {
                    $this->cprService->populateClient(new Cpr($properties['cpr']), $client);
                }
                break;

            default:
                throw new \RuntimeException(sprintf('Invalid client type: %s', $type));
        }

        foreach ($properties as $name => $value) {
            $this->propertyAccessor->setValue($client, $name, $value);
        }

        return $client;
    }
}

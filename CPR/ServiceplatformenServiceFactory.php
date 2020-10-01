<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use ItkDev\AzureKeyVault\Authorisation\VaultToken;
use ItkDev\AzureKeyVault\Exception\TokenException;
use ItkDev\AzureKeyVault\KeyVault\VaultSecret;
use ItkDev\Serviceplatformen\Certificate\AzureKeyVaultCertificateLocator;
use ItkDev\Serviceplatformen\Certificate\Exception\CertificateLocatorException;
use ItkDev\Serviceplatformen\Request\InvocationContextRequestGenerator;
use ItkDev\Serviceplatformen\Service\PersonBaseDataExtendedService;

/**
 * Class ServiceplatformenServiceFactory.
 */
class ServiceplatformenServiceFactory
{
    /**
     * Factory for CPR service.
     *
     * @param string $azureTenantId
     * @param string $azureApplicationId
     * @param string $azureClientSecret
     * @param string $azureKeyVaultName
     * @param string $azureKeyVaultSecret
     * @param string $azureKeyVaultSecretVersion
     * @param string $serviceplatformenServiceAgreementUuid
     * @param string $serviceplatformenUserSystemUuid
     * @param string $serviceplatformenUserUuid
     * @param string $personBaseDataExtendedServiceContract
     * @param string $personBaseDataExtendedServiceEndpoint
     * @param string $personBaseDataExtendedServiceUuid
     *
     * @return PersonBaseDataExtendedService
     *
     * @throws CprException
     */
    public static function createPersonBaseDataExtendedService(string $azureTenantId, string $azureApplicationId, string $azureClientSecret, string $azureKeyVaultName, string $azureKeyVaultSecret, string $azureKeyVaultSecretVersion, string $serviceplatformenServiceAgreementUuid, string $serviceplatformenUserSystemUuid, string $serviceplatformenUserUuid, string $personBaseDataExtendedServiceContract, string $personBaseDataExtendedServiceEndpoint, string $personBaseDataExtendedServiceUuid)
    {
        try {
            $token = VaultToken::getToken(
                $azureTenantId,
                $azureApplicationId,
                $azureClientSecret
            );
        } catch (TokenException $e) {
            throw new CprException($e->getMessage(), $e->getCode());
        }

        $vault = new VaultSecret(
            $azureKeyVaultName,
            $token->getAccessToken()
        );

        $certificateLocator = new AzureKeyVaultCertificateLocator(
            $vault,
            $azureKeyVaultSecret,
            $azureKeyVaultSecretVersion
        );

        try {
            $pathToCertificate = $certificateLocator->getAbsolutePathToCertificate();
        } catch (CertificateLocatorException $e) {
            throw new CprException($e->getMessage(), $e->getCode());
        }

        $options = [
            'local_cert' => $pathToCertificate,
            'passphrase' => $certificateLocator->getPassphrase(),
            'location' => $personBaseDataExtendedServiceEndpoint,
        ];

        if (!realpath($personBaseDataExtendedServiceContract)) {
            throw new CprException('The path to the service contract is invalid.');
        }

        try {
            $soapClient = new \SoapClient($personBaseDataExtendedServiceContract, $options);
        } catch (\SoapFault $e) {
            throw new CprException($e->getMessage(), $e->getCode());
        }

        $requestGenerator = new InvocationContextRequestGenerator(
            $serviceplatformenServiceAgreementUuid,
            $serviceplatformenUserSystemUuid,
            $personBaseDataExtendedServiceUuid,
            $serviceplatformenUserUuid
        );

        return new PersonBaseDataExtendedService($soapClient, $requestGenerator);
    }
}

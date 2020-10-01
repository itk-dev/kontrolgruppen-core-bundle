<?php

/*
 * This file is part of aakb/kontrolgruppen.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use ItkDev\AzureKeyVault\Authorisation\VaultToken;
use ItkDev\AzureKeyVault\KeyVault\VaultSecret;
use ItkDev\Serviceplatformen\Certificate\AzureKeyVaultCertificateLocator;
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
     * @return PersonBaseDataExtendedService
     *
     * @throws \ItkDev\AzureKeyVault\Exception\TokenException
     * @throws \ItkDev\Serviceplatformen\Certificate\Exception\CertificateLocatorException
     * @throws \SoapFault
     */
    public static function createPersonBaseDataExtendedService(string $azureTenantId, string $azureApplicationId, string $azureClientSecret, string $azureKeyVaultSecret, string $azureKeyVaultSecretVersion, string $serviceplatformenServiceAgreementUuid, string $serviceplatformenUserSystemUuid, string $serviceplatformenUserUuid, string $personBaseDataExtendedServiceEndpoint, string $personBaseDataExtendedServiceUuid)
    {
        $token = VaultToken::getToken(
            $azureTenantId,
            $azureApplicationId,
            $azureClientSecret
        );

        $vault = new VaultSecret(
            'kontrolgruppen',
            $token->getAccessToken()
        );

        // Contract is included in bundle, but you can change it if you have to.
        $pathToWsdl = __DIR__.'/../../../vendor/itk-dev/serviceplatformen/resources/person-base-data-extended-service-contract/wsdl/context/PersonBaseDataExtendedService.wsdl';

        $certificateLocator = new AzureKeyVaultCertificateLocator(
            $vault,
            $azureKeyVaultSecret,
            $azureKeyVaultSecretVersion
        );

        $options = [
            'local_cert' => $certificateLocator->getAbsolutePathToCertificate(),
            'passphrase' => $certificateLocator->getPassphrase(),
            'location' => $personBaseDataExtendedServiceEndpoint,
        ];

        $soapClient = new \SoapClient($pathToWsdl, $options);

        $requestGenerator = new InvocationContextRequestGenerator(
            $serviceplatformenServiceAgreementUuid,
            $serviceplatformenUserSystemUuid,
            $personBaseDataExtendedServiceUuid,
            $serviceplatformenUserUuid
        );

        return new PersonBaseDataExtendedService($soapClient, $requestGenerator);
    }
}

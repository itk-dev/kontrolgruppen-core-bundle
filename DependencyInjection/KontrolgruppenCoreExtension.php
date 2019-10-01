<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\DependencyInjection;

use Kontrolgruppen\CoreBundle\Export\Manager;
use Kontrolgruppen\CoreBundle\Security\SAMLAuthenticator;
use Kontrolgruppen\CoreBundle\Security\UserManager;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class KontrolgruppenCoreExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kontrolgruppen_core.net_default_value', $config['net_default_value']);

        $definition = $container->getDefinition(Manager::class);
        $definition->replaceArgument('$configuration', [
            'exports' => $config['exports'] ?? [],
            'export_directory' => $config['export_directory'] ?? null,
        ]);

        $definition = $container->getDefinition(UserManager::class);
        if (isset($config['user_class'])) {
            $definition->replaceArgument('$class', $config['user_class']);
        }

        $definition = $container->getDefinition(SAMLAuthenticator::class);
        if (isset($config['saml']['php_saml_settings'])) {
            $definition->replaceArgument('$settings', $config['saml']['php_saml_settings']);
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->loadFromExtension(
            'twig',
            [
                'default_path' => '%kernel.project_dir%/vendor/kontrolgruppen/core-bundle/Resources/views',
                'paths' => [
                    '%kernel.project_dir%/vendor/kontrolgruppen/core-bundle/Resources/views' => 'KontrolgruppenCoreBundle',
                ],
            ]
        );

        $container->prependExtensionConfig(
            'stof_doctrine_extensions',
            [
                'class' => [
                    'loggable' => 'Kontrolgruppen\CoreBundle\EventListener\LoggableListener',
                ],
            ]
        );
    }
}

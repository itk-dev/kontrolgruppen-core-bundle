<?php

namespace Kontrolgruppen\CoreBundle\DependencyInjection;

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
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->loadFromExtension(
            'twig',
            array(
                'default_path' => '%kernel.project_dir%/vendor/kontrolgruppen/core-bundle/Resources/views',
                'paths' => array(
                    '%kernel.project_dir%/vendor/kontrolgruppen/core-bundle/Resources/FOSUserBundle/views' => 'FOSUser',
                    '%kernel.project_dir%/vendor/kontrolgruppen/core-bundle/Resources/views' => 'KontrolgruppenCoreBundle',
                ),
            )
        );
    }
}

<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class CompilerPass.
 */
class CompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $loader = $this->getTwigLoader($container);
        if (null !== $loader) {
            $namespaces = ['Twig'];

            $paths = [];
            foreach ($namespaces as $namespace) {
                $paths[] = __DIR__.'/../../Resources/views/bundles/'.$namespace.'Bundle';
            }
            // We have to prepend path to app templates to allow overriding our bundle templates.
            if ($container->hasParameter('kernel.project_dir')) {
                foreach ($namespaces as $namespace) {
                    $path = $container->getParameter('kernel.project_dir').'/templates/bundles/'.$namespace.'Bundle';
                    if (is_dir($path)) {
                        $paths[] = $path;
                    }
                }
            }
            foreach ($paths as $path) {
                $loader->addMethodCall('prependPath', [$path, $namespace]);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return \Symfony\Component\DependencyInjection\Definition|null
     */
    private function getTwigLoader(ContainerBuilder $container)
    {
        foreach ([
            'twig.loader',
            'twig.loader.filesystem',
            'twig.loader.native_filesystem',
        ] as $id) {
            if ($container->hasDefinition($id)) {
                return $container->getDefinition($id);
            }
        }

        return null;
    }
}

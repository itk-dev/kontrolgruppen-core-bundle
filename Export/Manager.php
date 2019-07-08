<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Manager.
 */
class Manager
{
    /** @var \Symfony\Component\DependencyInjection\Container */
    protected $container;

    /**
     * The configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Constructor.
     */
    public function __construct(ContainerInterface $container, array $configuration = [])
    {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    public function getExports()
    {
        $exports = [];
        if (isset($this->configuration['exports'])) {
            foreach ($this->configuration['exports'] as $service) {
                $export = $this->getExport($service);
                if (null !== $export) {
                    $exports[\get_class($export)] = $export;
                }
            }
        }

        return $exports;
    }

    public function getExport($service)
    {
        $export = $this->container->get($service);

        return $export instanceof AbstractExport ? $export : null;
    }
}

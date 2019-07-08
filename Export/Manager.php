<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Export;

/**
 * Class Manager.
 */
class Manager
{
    /**
     * The configuration.
     *
     * @var array
     */
    protected $configuration;

    /**
     * Constructor.
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    public function getExports()
    {
        $exports = [];
        if (isset($this->configuration['exports'])) {
            foreach ($this->configuration['exports'] as $export) {
                $class = $export['class'] ?? $export;
                if (\is_string($class) && class_exists($class) && is_subclass_of($class, AbstractExport::class)) {
                    $exports[$class] = new $class();
                }
            }
        }

        return $exports;
    }
}

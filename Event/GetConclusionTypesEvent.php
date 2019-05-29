<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class GetConclusionTypesEvent extends Event
{
    const NAME = 'kontrolgruppen.core.get_conclusion_types';

    private $types;

    /**
     * GetConclusionTypesEvent constructor.
     *
     * @param array $types
     */
    public function __construct(array $types = [])
    {
        $this->types = $types;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function setTypes($types)
    {
        $this->types = $types;
    }
}

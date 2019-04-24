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

class GetConclusionTemplateEvent extends Event
{
    const NAME = 'kontrolgruppen.core.get_conclusion_template';

    private $class;
    private $template;

    /**
     * GetConclusionClasssEvent constructor.
     *
     * @param string
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }
}

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

/**
 * Class GetConclusionTemplateEvent.
 */
class GetConclusionTemplateEvent extends Event
{
    const NAME = 'kontrolgruppen.core.get_conclusion_template';

    private $class;
    private $template;
    private $action;

    /**
     * GetConclusionClasssEvent constructor.
     *
     * @param string $class
     * @param string $action
     */
    public function __construct($class, $action)
    {
        $this->class = $class;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
}

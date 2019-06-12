<?php


namespace Kontrolgruppen\CoreBundle\Entity;


interface ProcessLoggableInterface
{
    public function getProcess(): ?Process;
}

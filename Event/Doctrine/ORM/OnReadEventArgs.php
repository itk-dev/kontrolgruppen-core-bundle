<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Event\Doctrine\ORM;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Kontrolgruppen\CoreBundle\Entity\Process;

class OnReadEventArgs extends EventArgs
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected $process;

    /**
     * Constructor.
     */
    public function __construct(EntityManagerInterface $em, Process $process)
    {
        $this->em = $em;
        $this->process = $process;
    }

    /**
     * Retrieve associated EntityManager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    public function getProcess()
    {
        return $this->process;
    }
}

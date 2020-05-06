<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\StoreInterface;

/**
 * Class LockService.
 */
class LockService
{
    private $factory;
    private $locks = [];

    /**
     * LockService constructor.
     *
     * @param StoreInterface $store
     */
    public function __construct(StoreInterface $store)
    {
        $this->factory = new Factory($store);
    }

    /**
     * @return Factory
     */
    public function getFactory(): Factory
    {
        return $this->factory;
    }

    /**
     * @param string $resource
     *
     * @return Lock
     */
    public function createLock(string $resource): Lock
    {
        if (\array_key_exists($resource, $this->locks) && $this->locks[$resource] instanceof Lock) {
            return $this->locks[$resource];
        }

        $lock = $this->factory->createLock($resource);
        $this->locks[$resource] = $lock;

        return $lock;
    }

    /**
     * @param string $resource
     *
     * @return Lock
     */
    public function getLock(string $resource): Lock
    {
        if (!\array_key_exists($resource, $this->locks)) {
            throw new \InvalidArgumentException('Lock does not exist.');
        }

        return $this->locks[$resource];
    }

    /**
     * @param string $resource
     * @param bool   $blocking
     *
     * @return bool
     */
    public function acquire(string $resource, $blocking = false): bool
    {
        $lock = $this->getLock($resource);

        return $lock->acquire($blocking);
    }

    /**
     * @param string $resource
     *
     * @return bool
     */
    public function isAcquired(string $resource): bool
    {
        $lock = $this->getLock($resource);

        return $lock->isAcquired();
    }

    /**
     * @param string $resource
     */
    public function release(string $resource)
    {
        $lock = $this->getLock($resource);

        $lock->release();
    }
}

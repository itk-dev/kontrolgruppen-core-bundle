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
use Symfony\Component\Lock\Store\SemaphoreStore;
use Symfony\Component\Lock\StoreInterface;

class LockService
{
    private $factory;
    private $locks = [];

    public function __construct(StoreInterface $store)
    {
        //$store = new SemaphoreStore();
        $this->factory = new Factory($store);
    }

    public function getFactory(): Factory
    {
        return $this->factory;
    }

    public function createLock(string $resource): Lock
    {
        if (\array_key_exists($resource, $this->locks) && $this->locks[$resource] instanceof Lock) {
            return $this->locks[$resource];
        }

        $lock = $this->factory->createLock($resource);
        $this->locks[$resource] = $lock;

        return $lock;
    }

    public function getLock(string $resource): Lock
    {
        if (!\array_key_exists($resource, $this->locks)) {
            throw new \InvalidArgumentException('Lock does not exist.');
        }

        return $this->locks[$resource];
    }

    public function acquire(string $resource, $blocking = false): bool
    {
        $lock = $this->getLock($resource);

        return $lock->acquire($blocking);
    }

    public function isAcquired(string $resource): bool
    {
        $lock = $this->getLock($resource);

        return $lock->isAcquired();
    }

    public function release(string $resource)
    {
        $lock = $this->getLock($resource);

        $lock->release();
    }
}

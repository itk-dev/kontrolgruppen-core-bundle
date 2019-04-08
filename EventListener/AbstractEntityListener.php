<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Kontrolgruppen\CoreBundle\Entity\AbstractEntity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Events;

/**
 * Class AbstractEntityListener.
 */
class AbstractEntityListener
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Subscribed events.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * Sets updated/created at/by fields.
     *
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof AbstractEntity) {
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->tokenStorage->getToken()->getUser());
            $entity->setCreatedAt(new \DateTime());
            $entity->setCreatedBy($this->tokenStorage->getToken()->getUser());
        }
    }

    /**
     * Sets updated at/by fields.
     *
     * @param \Doctrine\Common\Persistence\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof AbstractEntity) {
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->tokenStorage->getToken()->getUser());
        }
    }
}

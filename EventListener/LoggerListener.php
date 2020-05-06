<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class LoggerListener.
 */
class LoggerListener implements EventSubscriberInterface
{
    private $authorizationChecker;
    private $tokenStorage;
    private $loggableListener;

    /**
     * LoggerListener constructor.
     *
     * @param LoggableListener                   $loggableListener
     * @param AuthorizationCheckerInterface|null $authorizationChecker
     * @param TokenStorageInterface|null         $tokenStorage
     */
    public function __construct(LoggableListener $loggableListener, AuthorizationCheckerInterface $authorizationChecker = null, TokenStorageInterface $tokenStorage = null)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->loggableListener = $loggableListener;
    }

    /**
     * On Kernel Request.
     *
     * @param KernelEvent $event
     */
    public function onKernelRequest(KernelEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (null === $this->tokenStorage || null === $this->authorizationChecker) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (null !== $token && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->loggableListener->setCreatorName($token);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}

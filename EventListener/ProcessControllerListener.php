<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\EventListener;

use Doctrine\Common\Persistence\ManagerRegistry;
use Kontrolgruppen\CoreBundle\Controller\ClientController;
use Kontrolgruppen\CoreBundle\Controller\ConclusionController;
use Kontrolgruppen\CoreBundle\Controller\EconomyEntryController;
use Kontrolgruppen\CoreBundle\Controller\JournalEntryController;
use Kontrolgruppen\CoreBundle\Controller\ProcessController;
use Kontrolgruppen\CoreBundle\Controller\ProcessLogController;
use Kontrolgruppen\CoreBundle\Controller\ProcessReminderController;
use Kontrolgruppen\CoreBundle\Controller\ProcessReportController;
use Kontrolgruppen\CoreBundle\Controller\RevenueController;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Event\Doctrine\ORM\OnReadEventArgs;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ProcessControllerListener implements EventSubscriberInterface
{
    private $doctrine;
    private $processRepository;

    public function __construct(ManagerRegistry $doctrine, ProcessRepository $processRepository)
    {
        $this->doctrine = $doctrine;
        $this->processRepository = $processRepository;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!\is_array($controller)) {
            return;
        }

        $controllers = [
            ProcessController::class,
            ProcessLogController::class,
            ProcessReminderController::class,
            ProcessReportController::class,
            RevenueController::class,
            ClientController::class,
            ConclusionController::class,
            EconomyEntryController::class,
            JournalEntryController::class,
        ];

        if (\in_array(\get_class($controller[0]), $controllers)) {
            if ($event->getRequest()->isMethod('GET')) {

                /** @var Process $process */
                $process = $event->getRequest()->attributes->get('process');

                // If the request is coming from inside the Process route group, we dont
                // dispatch the onRead event, as it already has been dispatched when visiting
                // the Process the first time.
                if (!$this->isRequestOriginatingFromProcessRouteGroup(
                    $event->getRequest(),
                    $process->getId()
                )
                ) {
                    $entityManager = $this->doctrine->getManager();
                    $eventManager = $entityManager->getEventManager();
                    $eventManager->dispatchEvent('onRead', new OnReadEventArgs($entityManager, $process));
                }
            }
        }
    }

    /**
     * Checks if a given request originates from a route in the given Process route group.
     *
     * @param Request $request
     * @param int     $processId
     *
     * @return bool
     */
    private function isRequestOriginatingFromProcessRouteGroup(Request $request, int $processId): bool
    {
        $inGroup = false;

        $referer = $request->headers->get('referer');
        if (!empty($referer)) {
            $refererRequestUri = Request::create($referer)->getRequestUri();

            $pattern = sprintf('/process\/%s/', $processId);

            if (preg_match($pattern, $refererRequestUri)) {
                $inGroup = true;
            }
        }

        return $inGroup;
    }
}

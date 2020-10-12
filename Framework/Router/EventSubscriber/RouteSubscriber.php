<?php


namespace Henri\Framework\Router\EventSubscriber;

use Henri\Framework\Router\Event\OnBeforeRouteEnterEvent;
use Henri\Framework\Router\HTTPRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RouteSubscriber implements EventSubscriberInterface {

    /**
     * @var HTTPRequest $HTTPRequest
     */
    private $HTTPRequest;

    /**
     * RequestSubscriber constructor.
     *
     * @param HTTPRequest $HTTPRequest
     */
    public function __construct(
        HTTPRequest $HTTPRequest

    ) {
        $this->HTTPRequest      = $HTTPRequest;
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
     * The code must not depend on runtime state as it will only be called at compile time.
     * All logic depending on runtime state must be put into the individual methods handling the events.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array {
        return array(
            OnBeforeRouteEnterEvent::class => 'onBeforeRouteEnter',
        );
    }

    /**
     * @param OnBeforeRouteEnterEvent $event
     * @param string $eventClassName
     * @param EventDispatcher $eventDispatcher
     *
     * @return void
     * @throws \Dibi\Exception
     */
    public function onBeforeRouteEnter( OnBeforeRouteEnterEvent $event, string $eventClassName, EventDispatcher $eventDispatcher ): void {
        return;
    }

}
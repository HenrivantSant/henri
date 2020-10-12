<?php


namespace Henri\Framework\Router\Event;

use Henri\Framework\Router\Route;
use Symfony\Contracts\EventDispatcher\Event;

class OnBeforeRouteEnterEvent extends Event {

    /**
     * @var Route $route
     */
    private $route;

    /**
     * OnBeforeRouteEnter constructor.
     *
     * @param $route
     */
    public function __construct( $route = '' ) {
        $this->route = $route;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route {
        return $this->route;
    }

    /**
     * @param Route $route
     */
    public function setRoute( Route $route ): void {
        $this->route = $route;
    }
}
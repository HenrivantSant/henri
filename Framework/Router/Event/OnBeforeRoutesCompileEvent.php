<?php


namespace Henri\Framework\Router\Event;

use Henri\Framework\Router\Route;
use Symfony\Contracts\EventDispatcher\Event;

class OnBeforeRoutesCompileEvent extends Event {

    /**
     * @var array $routes
     */
    private $routes;

    /**
     * @var array $matchTypes
     */
    private $matchTypes;

    /**
     * OnBeforeRoutesCompile constructor.
     *
     * @param array $routes
     * @param array $matchTypes
     */
    public function __construct( array $routes = array(), array $matchTypes = array() ) {
        $this->routes     = $routes;
        $this->matchTypes = $matchTypes;
    }

    /**
     * @param Route $route
     */
    public function addRoute(Route $route): void {
        $this->routes[] = $route;
    }

    /**
     * @return array
     */
    public function getRoutes(): array {
        return $this->routes;
    }

    /**
     * @param array $routes
     */
    public function setRoutes( array $routes ): void {
        $this->routes = $routes;
    }

    /**
     * @param string $identifier
     * @param string $regex
     */
    public function addMatchType(string $identifier, string $regex): void {
        if (array_key_exists($identifier, $this->matchTypes)) {
            throw new \InvalidArgumentException(sprintf('Match type %s is already declared', $identifier));
        }

        $this->matchTypes[$identifier] = $regex;
    }

    /**
     * @return array
     */
    public function getMatchTypes(): array {
        return $this->matchTypes;
    }

    /**
     * @param array $matchTypes
     */
    public function setMatchTypes( array $matchTypes ): void {
        $this->matchTypes = $matchTypes;
    }

}
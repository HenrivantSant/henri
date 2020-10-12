<?php


namespace Henri\Framework\Controller;


use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Router\Route;

abstract class AbstractController implements ControllerInterface {

    /**
     * @var Route $route
     */
    protected $route;

    /**
     * @var HTTPRequest $HTTPRequest
     */
    protected $HTTPRequest;

    /**
     * AbstractController constructor.
     *
     * @param HTTPRequest $HTTPRequest
     */
    public function __construct(
        HTTPRequest $HTTPRequest
    ) {
        $this->HTTPRequest = $HTTPRequest;
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

    /**
     * @return HTTPRequest
     */
    public function getHTTPRequest(): HTTPRequest {
        return $this->HTTPRequest;
    }
}
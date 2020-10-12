<?php


namespace Henri\Framework\Controller;


use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Router\Route;

interface ControllerInterface {

    public function getRoute(): Route;

    public function setRoute(Route $route): void;

    public function getHTTPRequest(): HTTPRequest;

}
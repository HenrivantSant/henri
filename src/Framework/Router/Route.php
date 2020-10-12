<?php


namespace Henri\Framework\Router;


class Route {

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var string $regex The route regex, custom regex must start with an @. You can use multiple pre-set regex filters, like [i:id]
     */
    public $regex;

    /**
     * @var string $controllerBase Controller base url/regex before the route
     */
    public $controllerBase;

    /**
     * @var array $methods HTTP methods on which the route applies. Methods must be one or more of 5 HTTP Methods (GET|POST|PATCH|PUT|DELETE)
     */
    public $methods = array();

    /**
     * @var string $controller FQCN of the controller class
     */
    public $controller;

    /**
     * @var string|null $action
     */
    public $action;

    /**
     * @var array $params
     */
    public $params;

    /**
     * @var bool $authRequired
     */
    public $authRequired;

    /**
     * @var int $authLevel
     */
    public $authLevel;

    /**
     * Route constructor.
     *
     * @param array $route
     */
    public function __construct( array $route = array()) {
        foreach ($route as $key => $item) {
            if (property_exists($this, $key)) {
                $this->{$key} = $item;
            }
        }
    }

    /**
     * Get full route regex including controller base
     *
     * @return string|null
     */
    public function getFullRegex(): ?string {
        if (is_null($this->regex)) {
            return null;
        }

        return is_null($this->controllerBase) ? $this->regex : $this->controllerBase . '/' . $this->regex;
    }

    /**
     * Checks whether a given HTTPMethod applies for this route
     *
     * @param string $method
     *
     * @return bool
     */
    public function methodApplies(string $method): bool {
        return in_array($method, $this->methods, false);
    }

}
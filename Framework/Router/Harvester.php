<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 14:06
 */

namespace Henri\Framework\Router;


use Henri\Framework\Annotations\Annotations;
use stdClass;


class Harvester
{

	/**
	 * @var Annotations $annotationService
	 */
	private $annotationService;

    /**
     * Harvest constructor.
     *
     * @param Annotations $annotationService
     */
	public function __construct(
			Annotations $annotationService
	) {
		$this->annotationService  = $annotationService;
	}

	/**
	 * Method to harvest routes from annotations
	 *
	 * @return array
	 */
	public function harvestRoutes(): array {
		$harvest = array();
		$controllers = $this->annotationService->getMethodAnnotationsByClasstag('Controller');

		if (empty($controllers)) {
			return $harvest;
		}

		foreach ($controllers as $controllerName => $controller) {
		    $controllerRoute = $this->extractRoute($controller->methods['__construct']);
			$baseRouteAuthRequired  = array_key_exists('__construct', $controller->methods) ? (bool) $controllerRoute->authRequired : false;
			$baseRouteAuthLevel     = array_key_exists('__construct', $controller->methods) ? $controllerRoute->authLevel : 'token';
			$baseRoute              = array_key_exists('__construct', $controller->methods) ? $controllerRoute->regex : '';

			foreach ($controller->methods as $method) {
				$route               = $this->extractRoute($method, $baseRoute);
				$route->authRequired = $baseRouteAuthRequired ? true : $route->authRequired;
				$route->authLevel    = $baseRouteAuthLevel === 'login' ? 'login' : $route->authLevel;
				$route->controller   = $controllerName;
				$route->action       = $method->name !== '__construct' ? $method->name : '';
				$harvest[]           = $route;
			}
		}

		return $harvest;
	}

	/**
	 * Method to extract route from method annotation
	 *
	 * @param stdClass $methodAnnotations
	 * @param string    $baseRoute
	 *
	 * @return Route
	 */
	private function extractRoute( stdClass $methodAnnotations, string $baseRoute = ''): Route {
		$baseRoute = trim($baseRoute, '/');
		$route = $methodAnnotations->annotations['Route']->vars['route'];
		$route = trim($route, '/');

		$type = $methodAnnotations->annotations['Route']->vars['type'];
		$authRequired   = (bool) $methodAnnotations->annotations['Route']->vars['authRequired'];
		$authLevel      = $methodAnnotations->annotations['Route']->vars['authLevel'];
		$name           = $methodAnnotations->annotations['Route']->vars['name'];

		return new Route(array(
            'regex' => $route,
            'controllerBase' => $baseRoute,
            'methods'  => explode('|', $type),
            'authRequired'  => $authRequired,
            'authLevel'     => $authLevel,
            'name'          => $name,
        ));
	}

}
<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 7-12-2019 11:55
 */

namespace Henri\Application;

use Exception;
use Henri\Framework\Configuration\Configuration;
use Henri\Framework\Controller\ControllerInterface;
use Henri\Framework\Events\EventDispatcher;
use Henri\Framework\Kernel\ContainerService\ContainerService;
use Henri\Framework\Http\Event\BeforeResponseEvent;
use Henri\Framework\Http\Response\JSONResponse;
use Henri\Framework\Http\Response\Response;
use Henri\Framework\Kernel\Event\KernelRequestEvent;
use Henri\Framework\Router\Event\OnBeforeRouteEnterEvent;
use Henri\Framework\Router\Exceptions\InternalErrorException;
use Henri\Framework\Router\Exceptions\NotAuthorizedException;
use Henri\Framework\Router\Exceptions\NotFoundException;
use Henri\Framework\Router\HTTPRequest;
use Henri\Framework\Router\Route;
use Henri\Framework\Router\Router;

class Application {

	/**
	 * @var ContainerService
	 */
	private $containerService;

	/**
	 * @var Router $router
	 */
	private $router;

	/**
	 * @var Configuration $configuration
	 */
	private $configuration;

    /**
     * @var HTTPRequest $HTTPRequest
     */
	private $HTTPRequest;

    /**
     * @var EventDispatcher $dispatcher
     */
	private $dispatcher;

    /**
     * Application constructor.
     *
     * @param Router $router
     * @param Configuration $configuration
     * @param HTTPRequest $HTTPRequest
     * @param EventDispatcher $dispatcher
     */
	public function __construct(
			Router              $router,
			Configuration       $configuration,
            HTTPRequest         $HTTPRequest,
            EventDispatcher     $dispatcher
	) {
		global $containerBuilder;
		$this->containerService = $containerBuilder;
		$this->router           = $router;
		$this->configuration    = $configuration;
		$this->HTTPRequest      = $HTTPRequest;
		$this->dispatcher       = $dispatcher;
	}

	/**
	 * Method to run application
	 *
	 * @throws Exception
	 */
	public function run() : void {
		try {
		    $this->dispatcher->dispatch(new KernelRequestEvent($this->HTTPRequest));

            $route = $this->router->getCurrentRoute();
            $response = $this->dispatch($route);
		} catch (NotFoundException $exception) {
		    $response = new JSONResponse();
			$response::notFound();
		} catch (NotAuthorizedException $exception) {
            $response = new JSONResponse();
		    $response::notAuthorized();
        } catch (InternalErrorException $exception) {
            $response = new JSONResponse();
		    $response::internalError();
        } catch ( Exception $exception) {
            if ($this->configuration->get('app.debug')) {
                throw $exception;
            }

            $response = new JSONResponse();
            $response::internalError();
        }

		$response->sendOutput();
	}

    /**
     * Method to dispatch requested route
     *
     * @param Route $route
     *
     * @return Response
     * @throws Exception
     */
	public function dispatch(Route $route): Response {
		if (!$this->containerService->has($route->controller)) {
			throw new NotFoundException('Not found', 404);
		}

        /**
         * @var Route $route
         */
		$route = ($this->dispatcher->dispatch(new OnBeforeRouteEnterEvent($route)))->getRoute();

        /**
         * @var ControllerInterface $controller
         */
		$controller = $this->containerService->get($route->controller);

		if (!$controller instanceof ControllerInterface) {
		    throw new NotFoundException('Invalid controller');
        }

		$controller->setRoute($route);

		if (empty($route->action) || !method_exists($controller, $route->action)) {
			throw new NotFoundException('Action not found');
		}

        /**
         * @var Response $response
         */
        $response = $controller->{$route->action}($route->params);
		$response = ($this->dispatcher->dispatch(new BeforeResponseEvent($response), BeforeResponseEvent::class))->getResponse();

		return $response;
	}


}
<?php


namespace Henri\Framework\Kernel\Event;

use Henri\Framework\Router\HTTPRequest;
use Symfony\Contracts\EventDispatcher\Event;

class KernelRequestEvent extends Event {

    /**
     * @var HTTPRequest $request
     */
    private $request;

    /**
     * KernelRequest constructor.
     *
     * @param $request
     */
    public function __construct( $request = '' ) {
        $this->request = $request;
    }

    /**
     * @return HTTPRequest
     */
    public function getRequest(): HTTPRequest {
        return $this->request;
    }

}
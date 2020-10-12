<?php

namespace Henri\Framework\Http\Event;

use Henri\Framework\Http\Response\Response;
use Symfony\Contracts\EventDispatcher\Event;


class BeforeResponseEvent extends Event {

    /**
     * @var Response $event
     */
    private $response;

    /**
     * BeforeResponseEvent constructor.
     *
     * @param response  Event data
     */
    public function __construct( $response = '' ) {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse( Response $response ): void {
        $this->response = $response;
    }


}
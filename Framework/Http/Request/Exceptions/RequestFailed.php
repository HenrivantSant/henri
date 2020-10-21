<?php


namespace Henri\Framework\Http\Request\Exceptions;


use Henri\Framework\Http\Request\Response;
use RuntimeException;
use Throwable;

class RequestFailed extends RuntimeException {

    /**
     * @var Response $reponse
     */
    private $reponse;
    
    public function __construct( string $message = "", Response $response = null, int $code = 0, Throwable $previous = null ) {
        $this->reponse = $response;
        
        parent::__construct( $message, $code, $previous );
    }

    /**
     * @return Response
     */
    public function getReponse(): Response {
        return $this->reponse;
    }

}
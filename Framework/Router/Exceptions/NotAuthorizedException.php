<?php


namespace Henri\Framework\Router\Exceptions;


class NotAuthorizedException extends \RuntimeException {

    protected $code = 401;

}
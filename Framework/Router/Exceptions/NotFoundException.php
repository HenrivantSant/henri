<?php


namespace Henri\Framework\Router\Exceptions;


class NotFoundException extends \RuntimeException {

    protected $code = 404;

}
<?php


namespace Henri\Framework\Logging;


use Henri\Framework\Events\EventDispatcher;
use Henri\Framework\Logging\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class SystemLogger extends Logger {

    /**
     * SystemLogger constructor.
     *
     * @param EventDispatcher $dispatcher
     */
    public function __construct( EventDispatcher $dispatcher ) {
        $stream = new StreamHandler(INCLUDE_DIR . '/var/system.log', Logger::DEBUG);
        $stream->setFormatter(new LineFormatter());

        parent::__construct($dispatcher, 'system', array($stream));
    }

}
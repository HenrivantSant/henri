<?php


namespace Henri\Framework\Logging;


use Henri\Framework\Events\EventDispatcher;
use Henri\Framework\Logging\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;

class AppLogger extends Logger {

    /**
     * AppLogger constructor.
     *
     * @param EventDispatcher $dispatcher
     */
    public function __construct( EventDispatcher $dispatcher ) {
        $stream = new StreamHandler(INCLUDE_DIR . '/var/app.log', Logger::DEBUG);
        $stream->setFormatter(new LineFormatter());

        parent::__construct($dispatcher, 'app', array($stream));
    }


}
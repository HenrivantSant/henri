<?php


namespace Henri\Framework\Logging;

use Henri\Framework\Events\EventDispatcher;
use Henri\Framework\Logging\Event\OnBeforeLoggerHandlersEvent;
use Henri\Framework\Logging\Formatter\LineFormatter;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\StreamHandler;
use Henri\Framework\Logging\Handler\DBHandler;
use Henri\Framework\Logging\Logger;

class UserLogger extends Logger {

    /**
     * UserLogger constructor.
     *
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher) {
        $stream = new StreamHandler(INCLUDE_DIR . '/var/my_app.log', Logger::DEBUG);
        $stream->setFormatter(new LineFormatter());

        $db = new DBHandler(Logger::DEBUG);
        
        parent::__construct($dispatcher, 'users', array($stream, $db));
    }
}
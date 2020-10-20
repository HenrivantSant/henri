<?php


namespace Henri\Framework\Logging;


use Henri\Framework\Events\EventDispatcher;
use Henri\Framework\Logging\Formatter\LineFormatter;
use Henri\Framework\Logging\Handler\DBHandler;
use Monolog\Handler\StreamHandler;

class DBLogger extends Logger {

    /**
     * AppLogger constructor.
     *
     * @param EventDispatcher $dispatcher
     */
    public function __construct( EventDispatcher $dispatcher, DBHandler $dbHandler ) {
        $dbHandler->setFormatter(new LineFormatter());

        parent::__construct($dispatcher, 'app', array($dbHandler));
    }


}
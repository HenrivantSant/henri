<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 10:44
 */

namespace Henri\Application\Bootstrap;

use Henri\Application\Bootstrap\Autoloading\Autoloader;
use Henri\Application\Bootstrap\DependencyInjection\DependencyInjection;

class Bootstrap {

    /**
     * Bootstrap Application
     */
    public function initialize(): void {
        /**
         * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
         */
        define('HENRI_MINIMUM_PHP', '7.3.0');
        
        if (version_compare(PHP_VERSION, HENRI_MINIMUM_PHP, '<')) {
            die('Your host needs to use PHP ' . HENRI_MINIMUM_PHP . ' or higher to run this version of Henri!');
        }

        // Saves the start time and memory usage.
        global $startTime;
        global $startMem;
        $startTime = microtime(true);
        $startMem  = memory_get_usage();

        date_default_timezone_set('Europe/Amsterdam');


        // set up autoloading
        $autoloadBootstrap = new Autoloader();
        $autoloadBootstrap->initialize();

        // set up DI
        $DIBootstrap = new DependencyInjection();
        $DIBootstrap->initialize();
    }
    
}
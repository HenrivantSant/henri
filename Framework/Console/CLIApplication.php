<?php


namespace Henri\Framework\Console;

if (!defined('INCLUDE_DIR')) {
    define('INCLUDE_DIR', dirname(__DIR__, 5));
}

require_once INCLUDE_DIR . '/vendor/autoload.php';

use Exception;
use Henri\Application\Bootstrap\Autoloading\Autoloader;
use Henri\Application\Bootstrap\DependencyInjection\DependencyInjection;

class CLIApplication {

    /**
     * Bootstrap CLI Application
     */
    public function run(): void {
        if (PHP_SAPI !== 'cli') {
            echo 'bin/console must be run as a CLI application';
            exit(1);
        }

        try {
            // set up autoloading
            $autoloaderBootstrap = new Autoloader();
            $autoloaderBootstrap->initialize();
            
            // set up DI
            $DIBootstrap = new DependencyInjection();
            $DIBootstrap->initialize();
        } catch (Exception $e) {
            echo 'Autoload error: ' . $e->getMessage();
            exit(1);
        }

        try {
            // Build to application
            global $containerBuilder;
            $app = $containerBuilder->get('Henri\Framework\Console\Console');
            $app->run();
        } catch (Exception $e) {
            while($e) {
                echo $e->getMessage();
                echo $e->getTraceAsString();
                echo "\n\n";
                $e->getPrevious();
            }
            exit(0);
        }
    }

}
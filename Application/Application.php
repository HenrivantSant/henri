<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 7-12-2019 11:55
 */

namespace Henri\Application;

if (!defined('INCLUDE_DIR')) {
    define('INCLUDE_DIR', dirname(__DIR__, 4));
}

use Henri\Application\Bootstrap\Bootstrap;
use Exception;

class Application {

	/**
	 * Method to run application
	 *
	 * @throws Exception
	 */
	public function run() : void {
        $bootstrap = new Bootstrap();
        $bootstrap->initialize();

        // Build to application
        global $containerBuilder;
        $app = $containerBuilder->get('Henri\Framework\Kernel\Application');
        $app->run();
	}


}
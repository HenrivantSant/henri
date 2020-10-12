<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 10:44
 */

// set up autoloading
include_once INCLUDE_DIR . '/vendor/henrivantsant/henri/src/Application/Bootstrap/Autoloading/Autoload.php';

// set up DI
include_once INCLUDE_DIR . '/vendor/henrivantsant/henri/src/Application/Bootstrap/DependencyInjection/DependencyInjection.php';

// Build to application
global $containerBuilder;
$app = $containerBuilder->get('Henri\Application\Application');
$app->run();
<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 11:06
 */

namespace Henri\Application\Bootstrap\Autoloading;

require_once INCLUDE_DIR . '/vendor/autoload.php';

class Autoloader {

    /**
     * Register autoloaders
     */
    public function initialize(): void {
        spl_autoload_register(function($className) {
            $classPathLc = lcfirst(str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php');
            $classPathUc = ucfirst(str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php');

            // Check if class exists
            if (file_exists(INCLUDE_DIR . '/vendor/' . $classPathLc)) {
                include_once INCLUDE_DIR . '/vendor/' . $classPathLc;
            }
            if (file_exists(INCLUDE_DIR . '/vendor/' . $classPathUc)) {
                include_once INCLUDE_DIR . '/vendor/' . $classPathUc;
            }
            if (file_exists(INCLUDE_DIR . '/src/' . $classPathLc)) {
                include_once INCLUDE_DIR . '/src/' . $classPathLc;
            }
            if (file_exists(INCLUDE_DIR . '/src/' . $classPathUc)) {
                include_once INCLUDE_DIR . '/src/' . $classPathUc;
            }
            if (file_exists(INCLUDE_DIR . '/app/' . $classPathLc)) {
                include_once INCLUDE_DIR . '/app/' . $classPathLc;
            }
            if (file_exists(INCLUDE_DIR . '/app/' . $classPathUc)) {
                include_once INCLUDE_DIR . '/app/' . $classPathUc;
            }
        });
    }
    
}
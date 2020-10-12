<?php
/**
 * Created by Buro26.
 * Author: Henri
 * Date: 14-12-2019 11:06
 */

require INCLUDE_DIR . '/vendor/autoload.php';

spl_autoload_register(function($className) {
	$classPath = lcfirst(str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php');

	// Check if class exists
	if (file_exists(INCLUDE_DIR . '/vendor/' . $classPath)) {
		include_once INCLUDE_DIR . '/vendor/' . $classPath;
	}
	if (file_exists(INCLUDE_DIR . '/src/' . $classPath)) {
		include_once INCLUDE_DIR . '/src/' . $classPath;
	}
	if (file_exists(INCLUDE_DIR . '/app/' . $classPath)) {
		include_once INCLUDE_DIR . '/app/' . $classPath;
	}
});
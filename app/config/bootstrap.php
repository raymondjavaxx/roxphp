<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Path to application folder
 */
define('ROX_APP_PATH', dirname(__DIR__));

define('ROX_LIBRARIES_PATH', dirname(ROX_APP_PATH).'/libraries');

/**
 * Path to framework folder
 */
define('ROX_FRAMEWORK_PATH', ROX_LIBRARIES_PATH.'/rox');

// Set include paths
set_include_path(implode(PATH_SEPARATOR, array(
	ROX_APP_PATH . '/models',
	ROX_APP_PATH . '/controllers',
	ROX_APP_PATH . '/helpers',
	ROX_APP_PATH . '/mailers',
	ROX_APP_PATH . '/libraries',
	ROX_LIBRARIES_PATH,
	// uncoment the line below preserve default include paths
	// get_include_path()
)));

// Set error reporting level
error_reporting(0);

if (PHP_SAPI !== 'cli') {
	// Load and set the exception handler
	require ROX_FRAMEWORK_PATH . '/exception/Handler.php';
	\rox\exception\Handler::register();
}

// Treat errors as exceptions
// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
// 	throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
// });

// Load and register the autoloader
require ROX_FRAMEWORK_PATH . '/Loader.php';
\rox\Loader::register();

// Load DB configuration file and init
require ROX_APP_PATH . '/config/environment.php';
require ROX_APP_PATH . '/config/database.php';
require ROX_APP_PATH . '/config/routes.php';
require ROX_APP_PATH . '/config/init.php';

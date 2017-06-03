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
define('ROX_APP_PATH', dirname(__DIR__) . '/app');

// Set error reporting level
error_reporting(0);

require dirname(__DIR__) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
	\rox\exception\Handler::register();
}

// Treat errors as exceptions
// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
// 	throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
// });

// Load DB configuration file and init
require __DIR__ . '/environment.php';
require __DIR__ . '/log.php';
require __DIR__ . '/database.php';
require __DIR__ . '/routes.php';
require __DIR__ . '/init.php';

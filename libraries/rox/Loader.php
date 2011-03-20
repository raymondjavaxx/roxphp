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
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox;

/**
 * Loader
 *
 * @package Rox
 */
class Loader {

	/**
	 * Register the autoloader function
	 *
	 * @return void
	 */
	public static function register() {
		spl_autoload_register(array(get_called_class(), 'loadClass'));
	}

	/**
	 * autoloader function
	 *
	 * @param string $class class name
	 * @return void
	 * @link http://groups.google.com/group/php-standards/web/psr-0-final-proposal
	 */
	public static function loadClass($class) {
		$class = ltrim($class, '\\');
		$fileName  = '';
		$namespace = '';
		if ($lastNsPos = strripos($class, '\\')) {
			$namespace = substr($class, 0, $lastNsPos);
			$class = substr($class, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		require_once $fileName;
	}
}

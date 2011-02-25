<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
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
	 */
	public static function loadClass($class) {
		$filename = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
		require_once $filename;
	}
}

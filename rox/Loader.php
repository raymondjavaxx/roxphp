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

/**
 * Rox_Loader
 *
 * @package Rox
 */
class Rox_Loader {

	/**
	 * Register the autoloader function
	 *
	 * @return void
	 */
	public static function register() {
		spl_autoload_register(array('Rox_Loader', 'loadClass'));
	}

	/**
	 * autoloader function
	 *
	 * @param string $class class name
	 * @return void
	 */
	public static function loadClass($class) {
		if (strpos($class, 'Rox_') === 0) {
			$filename = implode(DIRECTORY_SEPARATOR, explode('_', substr($class, 4))) . '.php';
			require_once ROX_FRAMEWORK_PATH . DIRECTORY_SEPARATOR . $filename;
		} else {
			$filename = implode(DIRECTORY_SEPARATOR, explode('_', $class)) . '.php';
			require_once $filename;
		}
	}
}

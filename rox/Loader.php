<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Rox_Loader
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
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

<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id: $
 */

/**
 * Rox class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox {

	private static $helperInstances = array();

	/**
	 * Returns instance of a model
	 *
	 * @param string $name
	 * @return object
	 */
	public static function getModel($name) {
		if (!class_exists($name)) {
			Rox::loadModel($name);
		}

		return new $name;
	}

	/**
	 * Loads a model
	 *
	 * @param string $name
	 */
	public static function loadModel($name) {
		require_once(MODELS . strtolower($name) . '.php');
	}

	/**
	 * Returns a singleton instance of a helper 
	 *  
	 * @param string $name
	 * @return object
	 */
	public static function getHelper($name) {
		if (!isset(self::$helperInstances[$name])) {
			Rox::loadHelper($name);
			$className = $name . 'Helper';
			self::$helperInstances[$name] = new $className();
		}

		return self::$helperInstances[$name];
	}

	/**
	 * Loads a helper
	 *
	 * @param string $name
	 */
	public static function loadHelper($name) {
		require_once(HELPERS . strtolower($name) . '.php');
	}
}
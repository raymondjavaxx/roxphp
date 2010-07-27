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
 * Rox class
 *
 * @package Rox
 */
class Rox {

	private static $_helperInstances = array();

	/**
	 * Returns a singleton instance of a helper 
	 *  
	 * @param string $name
	 * @return object
	 */
	public static function getHelper($name) {
		if (!isset(self::$_helperInstances[$name])) {
			$className = self::_loadHelper($name);
			if (!class_exists($className)) {
				throw new Rox_Exception("Class '{$className}' not found");
			}

			self::$_helperInstances[$name] = new $className();
		}

		return self::$_helperInstances[$name];
	}

	/**
	 * Loads a helper
	 *
	 * @param string $name
	 */
	protected static function _loadHelper($name) {
		$name = Rox_Inflector::camelize($name);

		$file = ROX_APP_PATH . "/helpers/{$name}Helper.php";
		if (file_exists($file)) {
			require_once $file;
			return $name . 'Helper';
		}

		$file = ROX_FRAMEWORK_PATH . "/Helper/{$name}.php";
		if (file_exists($file)) {
			require_once $file;
			return 'Rox_Helper_' . $name;
		}

		throw new Rox_Exception("Helper '{$name}' not found");
	}
}

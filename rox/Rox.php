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
 * @version $Id$
 */

/**
 * Rox class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
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
				throw new Exception("Class '$className' not found");
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

		$file = ROX_APP_PATH.DS.'helpers'.DS.$name.'Helper.php';
		if (file_exists($file)) {
			require_once $file;
			return $name.'Helper';
		}

		$file = ROX_FRAMEWORK_PATH.DS.'Helper'.DS.$name.'.php';
		if (file_exists($file)) {
			require_once $file;
			return 'Rox_Helper_' . $name;
		}

		throw new Exception("Helper '$name' not found");
	}
}

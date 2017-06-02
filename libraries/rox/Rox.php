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

use \rox\Exception;

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
		$name = Inflector::camelize($name);

		if (class_exists("\\App\\Helpers\\{$name}Helper")) {
			return "\App\\Helpers\\{$name}Helper";
		}

		if (class_exists("\\rox\\template\\helper\\{$name}")) {
			return "\\rox\\template\\helper\\{$name}";
		}

		throw new Exception("Helper '{$name}' not found");
	}
}

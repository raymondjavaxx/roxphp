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
 * Registry
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Registry {

	/**
	 * Object instances
	 *
	 * @var array
	 */
	private static $_objects = array();

	/**
	 * Adds an object to the registry
	 *
	 * @param string $name
	 * @param object $instance
	 * @return boolean
	 */
	public static function addObject($name, $instance) {
		if (!self::contains($name)) {
			self::$_objects[$name] = $instance;
			return true;
		}

		return false;
	}

	/**
	 * Retrieves an object from the registry 
	 *
	 * @param string $name
	 * @return object|false
	 */
	public static function getObject($name) {
		if (self::contains($name)) {
			return self::$_objects[$name];
		}

		return false;
	}

	/**
	 * Removes an object from the registry
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public static function removeObject($name) {
		if (self::contains($name)) {
			unset(self::$_objects[$name]);
			return true;
		}

		return false;
	}

	/**
	 * Checks if the registry contains a given object 
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public static function contains($name) {
		return array_key_exists($name, self::$_objects);
	}

	/**
	 * Returns the number of objects in the registry
	 * 
	 * @return integer
	 */
	public static function size() {
		return count(self::$_objects);
	}
}
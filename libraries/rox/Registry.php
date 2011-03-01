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

/**
 * Registry
 *
 * @package Rox
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
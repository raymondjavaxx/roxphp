<?php
/**
 * Registry
 *  
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Registry extends Object {

	private $objects = array();

	private static $_instance;

  /**
   * Registry::getInstance()
   *
   * @return
   */
	public static function &getInstance() {
		if (empty(self::$_instance)) {
			self::$_instance = new Registry();
		}

		return self::$_instance;
	}

  /**
   * Registry::addObject()
   *
   * @param mixed $name
   * @param mixed $instance
   * @return
   */
	public static function addObject($name, &$instance) {
		$_self = Registry::getInstance();
		if (!isset($_self->objects[$name])) {
			$_self->objects[$name] = &$instance;
			return true;
		}

		return false;
	}

  /**
   * Registry::getObject()
   *
   * @param mixed $name
   * @return
   */
	public static function &getObject($name) {
		$_self = Registry::getInstance();
		if (isset($_self->objects[$name])) {
			return $_self->objects[$name];
		}

		$result = false;
		return $result;
	}
}
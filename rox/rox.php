<?php
/**
 * Rox class
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Rox extends Object {

  /**
   * Returns instance of a model
   *
   * @param string $name
   * @return object
   */
	static function getModel($name) {
		if (class_exists($name)) {
			return new $name;
		}
		return false;
	}

  /**
   * Loads a model
   *
   * @param string $name
   */
	static function loadModel($name) {
		require_once(MODELS . DS . strtolower($name) . '.php');
	}
}
?>
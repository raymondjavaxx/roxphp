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
}
<?php
/**
 * Router
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
class Router extends Object {

  /**
   * Router::url()
   *
   * @param string $path
   * @return string
   */
	public static function url($path) {
		return WWW . $path;
	}
}
<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Router
 *  
 * @package Rox
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Router {

	/**
	 * Rox_Router::url()
	 *
	 * @param string $path
	 * @return string
	 */
	public static function url($path, $absolute = false) {
		if ($absolute) {
			return self::getBaseUrl() . WWW . $path;
		}

		return WWW . $path;
	}

	/**
	 * Returns the base URL
	 *
	 * @return string
	 */
	public static function getBaseUrl() {
		static $baseUrl;
		if ($baseUrl === null) {
			//TODO: https
			$baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
		}

		return $baseUrl;
	}

	/**
	 * undocumented function
	 *
	 * @param string $url
	 * @return array
	 * @throws Exception
	 */
	public static function parseUrl($url) {
		$parts = explode('/', trim($url, '/'));

		if (preg_match('/^[a-z_]+$/', $parts[0]) != 1) {
			throw new Exception('Illegal controller name', 404);
		}

		if (isset($parts[1]) && preg_match('/^[a-z_]+$/', $parts[1]) != 1) {
			throw new Exception('Illegal action name', 404);
		}

		$result = array(
			'controller' => Rox_Inflector::camelize($parts[0]).'Controller',
			'action'     => isset($parts[1]) ? Rox_Inflector::lowerCamelize($parts[1]).'Action' : 'indexAction',
			'params'     => array_slice($parts, 2)
		);

		return $result;	
	}
}

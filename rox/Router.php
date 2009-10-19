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
	 * Routing config
	 *
	 * @var array
	 */
	protected static $_config = array(
		'prefixes' => array('admin')
	);

	/**
	 * Sets the configuration for the routing
	 *
	 * @param array $config 
	 */
	public static function setConfig(array $config) {
		self::$_config = array_merge(self::$_config, $config);
	}

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

		$actionPrefix = null;
		if (in_array($parts[0], self::$_config['prefixes'])) {
			$actionPrefix = array_shift($parts);
		}

		if (preg_match('/^[a-z_]+$/', $parts[0]) != 1) {
			throw new Exception('Illegal controller name', 404);
		}

		$action = 'index';
		if (isset($parts[1])) {
			if (preg_match('/^[a-z_]+$/', $parts[1]) != 1) {
				throw new Exception('Illegal action name', 404);
			}
			$action = $parts[1];
		}

		$result = array(
			'controller'    => $parts[0],
			'action'        => $action,
			'controller_class' => Rox_Inflector::camelize($parts[0]) . 'Controller',
			'action_method' => Rox_Inflector::lowerCamelize($actionPrefix . $action) . 'Action',
			'params'        => array_slice($parts, 2),
			'prefix'        => $actionPrefix
		);

		return $result;	
	}
}

<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Router
 *  
 * @package Rox
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
			return self::getBaseUrl() . self::base() . $path;
		}

		return self::base() . $path;
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
	 * Returns the base path
	 *
	 * @return string
	 */
	public static function base() {
		static $base = false;

		if ($base === false) {
			$base = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
			$base = rtrim(str_replace(array('/app/webroot', '/webroot'), '', $base), '/');
		}

		return $base;
	}

	/**
	 * undocumented function
	 *
	 * @param string $url
	 * @return array
	 * @throws Rox_Exception
	 */
	public static function parseUrl($url) {
		$parts = explode('/', trim($url, '/'));

		$actionPrefix = null;
		if (in_array($parts[0], self::$_config['prefixes'])) {
			$actionPrefix = array_shift($parts);
		}

		$extension = 'html';
		$lastPart = array_pop($parts);
		if ($lastPart !== null) {
			if (preg_match('/(?<param>.*)\.(?<extension>[a-z0-9]{1,32})/', $lastPart, $matches) == 1) {
				$lastPart = $matches['param'];
				$extension = $matches['extension'];
			}

			array_push($parts, $lastPart);
		}

		if (preg_match('/^[a-z_]+$/', $parts[0]) != 1) {
			throw new Rox_Exception('Illegal controller name', 404);
		}

		$action = 'index';
		if (isset($parts[1])) {
			if (preg_match('/^[a-z_]+$/', $parts[1]) != 1) {
				throw new Rox_Exception('Illegal action name', 404);
			}
			$action = $parts[1];
		}

		$actionMethod = ($actionPrefix == null ? $action : $actionPrefix . '_' . $action);
		$actionMethod = Rox_Inflector::lowerCamelize($actionMethod) . 'Action';
		$controllerClass = Rox_Inflector::camelize($parts[0]) . 'Controller';

		$result = array(
			'controller'    => $parts[0],
			'action'        => $action,
			'controller_class' => $controllerClass,
			'action_method' => $actionMethod,
			'params'        => array_slice($parts, 2),
			'prefix'        => $actionPrefix,
			'extension'     => $extension
		);

		return $result;	
	}
}

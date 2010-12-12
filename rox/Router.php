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

	protected static $_matches = array();
	protected static $_routes = array();

	public static function match($path, $params) {
		self::$_matches[$path] = $params;
	}

	/**
	 * Connects a route
	 *
	 * @param string $template 
	 * @param array $params 
	 * @param array $options 
	 * @return void
	 */
	public static function connect($template, $params, $options = array()) {
		self::$_routes[] = new Rox_Route(compact('template', 'params', 'options'));
	}

	/**
	 * Connects the homepage
	 *
	 * @param array $params 
	 * @return void
	 */
	public static function connectRoot($params) {
		self::match('/', $params);
	}

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
	 * Creates restful routes for controller
	 *
	 * @param string $name controller name
	 * @param string $namespace (optional)
	 * @return void
	 */
	public static function resource($name, $namespace = false) {
		if (strpos($name, '.') !== false) {
			$resource = array();

			$controllers = explode('.', $name);
			$lastController = array_pop($controllers);

			foreach ($controllers as $key => $value) {
				$resource[] = $value;
				$resource[] = ':' . Rox_Inflector::singularize($value) . '_id';
			}

			$resource[] = $lastController;

			$resource = implode('/', $resource);
			$controller = ($namespace === false) ? $lastController : "{$lastController}_{$name}";
		} else {
			$resource = ($namespace === false) ? $name : "{$namespace}/{$name}";
			$controller = ($namespace === false) ? $name : "{$namespace}_{$name}";
		}

		self::connect("/{$resource}", array('controller' => $controller, 'action' => 'add', 'namespace' => $namespace), array('via' => 'POST'));
		self::connect("/{$resource}", array('controller' => $controller, 'action' => 'index', 'namespace' => $namespace));
		self::connect("/{$resource}/new", array('controller' => $controller, 'action' => 'add', 'namespace' => $namespace));
		self::connect("/{$resource}/:id/edit", array('controller' => $controller, 'action' => 'edit', 'namespace' => $namespace));
		self::connect("/{$resource}/:id", array('controller' => $controller, 'action' => 'edit', 'namespace' => $namespace),array('via' => 'PUT'));
		self::connect("/{$resource}/:id", array('controller' => $controller, 'action' => 'delete', 'namespace' => $namespace),array('via' => 'DELETE'));
		self::connect("/{$resource}/:id", array('controller' => $controller, 'action' => 'view', 'namespace' => $namespace));
	}

	public static function parseUrl($url) {
		if ($params = self::_parseUrl($url)) {
			$defaults = array('action' => 'index', 'extension' => 'html', 'namespace' => false, 'args' => array());
			$params += $defaults;

			$params['action_method'] = Rox_Inflector::lowerCamelize($params['action']) . 'Action';
			$params['controller_class'] = Rox_Inflector::camelize($params['controller']) . 'Controller';

			//print_r($params);exit;
			return $params;
		}

		return $params;
	}

	protected static function _parseUrl($url) {
		$url = '/' . trim($url, '/');

		if (isset(self::$_matches[$url])) {
			return self::$_matches[$url];
		}

		foreach (self::$_routes as $route) {
			if ($params = $route->match($url)) {
				return $params;
			}
		}

		return false;
	}
}

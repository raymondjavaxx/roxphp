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

namespace rox;

use \rox\http\Request;

/**
 * Router
 *  
 * @package Rox
 */
class Router {

	protected static $_matches = array();
	protected static $_routes = array();

	public static function match($path, $params) {
		self::$_matches[$path] = $params;
	}

	/**
	 * Nicer version of Router::connect()
	 *
	 * @param string $method HTTP method
	 * @param string $template URI template
	 * @param string $params
	 * @param string $options
	 * @return void
	 */
	public static function on($method, $template, $params, $options = array()) {
		static::connect($template, $params, $options + array('via' => $method));
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
		self::$_routes[] = new Route(compact('template', 'params', 'options'));
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
	 * Router::url()
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
	 * @param array $options
	 * @return void
	 */
	public static function resource($name, $options = array()) {
		$defaults = array('namespace' => false, 'only' => array(), 'except' => array());
		$options = array_merge($defaults, $options);

		$whitelist = empty($options['only']) ? array('index', 'view', 'add', 'edit', 'delete') : (array)$options['only'];
		if (!empty($options['except'])) {
			$whitelist = array_diff($whitelist, (array)$options['except']);
		}

		$whitelist = array_flip($whitelist);

		if (strpos($name, '.') !== false) {
			$resource = array();
			if ($options['namespace'] !== false) {
				$resource[] = $options['namespace'];
			}

			$controllers = explode('.', $name);
			$lastController = array_pop($controllers);

			foreach ($controllers as $key => $value) {
				$resource[] = $value;
				$resource[] = ':' . Inflector::singularize($value) . '_id';
			}

			$resource[] = $lastController;

			$resource = implode('/', $resource);
			$controller = ($options['namespace'] === false) ? $lastController : "{$options['namespace']}_{$lastController}";
		} else {
			$resource = ($options['namespace'] === false) ? $name : "{$options['namespace']}/{$name}";
			$controller = ($options['namespace'] === false) ? $name : "{$options['namespace']}_{$name}";
		}

		$map = array(
			'add' => array(
				array('method' => 'GET', 'template' => "/{$resource}/new"),
				array('method' => 'POST', 'template' => "/{$resource}")
			),
			'index' => array(
				array('method' => 'GET', 'template' => "/{$resource}")
			),
			'edit' => array(
				array('method' => 'GET', 'template' => "/{$resource}/:id/edit"),
				array('method' => 'PUT', 'template' => "/{$resource}/:id")
			),
			'delete' => array(
				array('method' => 'DELETE', 'template' => "/{$resource}/:id")
			),
			'view' => array(
				array('method' => 'GET', 'template' => "/{$resource}/:id")
			),
		);

		foreach ($map as $action => $specs) {
			if (isset($whitelist[$action])) {
				foreach ($specs as $spec) {
					static::on($spec['method'], $spec['template'], array(
						'controller' => $controller,
						'action'     => $action,
						'namespace'  => $options['namespace']
					));
				}
			}
		}
	}

	public static function parseUrl($url, Request $request = null) {
		if ($params = self::_parseUrl($url, $request)) {
			$defaults = array('action' => 'index', 'extension' => 'html', 'namespace' => false, 'args' => array());
			$params += $defaults;

			$params['action_method'] = Inflector::lowerCamelize($params['action']) . 'Action';
			$params['controller_class'] = 'App\\Controllers\\' . Inflector::camelize($params['controller']) . 'Controller';

			return $params;
		}

		return $params;
	}

	protected static function _parseUrl($url, $request) {
		$url = '/' . trim($url, '/');

		if (isset(self::$_matches[$url])) {
			return self::$_matches[$url];
		}

		foreach (self::$_routes as $route) {
			if ($params = $route->match($url, $request)) {
				return $params;
			}
		}

		return false;
	}
}

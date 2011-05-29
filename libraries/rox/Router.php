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

		if (isset($whitelist['add'])) {
			self::connect("/{$resource}", array(
				'controller' => $controller,
				'action' => 'add',
				'namespace' => $options['namespace']
			), array( 'via' => 'POST'));

			self::connect("/{$resource}/new", array(
				'controller' => $controller,
				'action' => 'add',
				'namespace' => $options['namespace']
			), array('via' => 'GET'));
		}

		if (isset($whitelist['index'])) {
			self::connect("/{$resource}", array(
				'controller' => $controller,
				'action' => 'index',
				'namespace' => $options['namespace']
			));
		}

		if (isset($whitelist['edit'])) {
			self::connect("/{$resource}/:id/edit", array(
				'controller' => $controller,
				'action' => 'edit',
				'namespace' => $options['namespace']
			));

			self::connect("/{$resource}/:id", array(
				'controller' => $controller,
				'action' => 'edit',
				'namespace' => $options['namespace']
			), array('via' => 'PUT'));
		}

		if (isset($whitelist['delete'])) {
			self::connect("/{$resource}/:id", array(
				'controller' => $controller,
				'action' => 'delete',
				'namespace' => $options['namespace']
			), array('via' => 'DELETE'));
		}

		if (isset($whitelist['view'])) {
			self::connect("/{$resource}/:id", array(
				'controller' => $controller,
				'action' => 'view',
				'namespace' => $options['namespace']
			));
		}
	}

	public static function parseUrl($url, Request $request = null) {
		if ($params = self::_parseUrl($url, $request)) {
			$defaults = array('action' => 'index', 'extension' => 'html', 'namespace' => false, 'args' => array());
			$params += $defaults;

			$params['action_method'] = Inflector::lowerCamelize($params['action']) . 'Action';
			$params['controller_class'] = Inflector::camelize($params['controller']) . 'Controller';

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

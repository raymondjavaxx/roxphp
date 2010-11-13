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
 * Dispatcher
 *
 * @package Rox
 */
class Rox_Dispatcher {

	/**
	 * Rox_Dispatcher::dispatch()
	 *
	 * @param mixed $url
	 * @throws Rox_Exception
	 */
	public function dispatch($url = null) {
		$parsedUrl = Rox_Router::parseUrl($url);

		$this->_loadController($parsedUrl);

		$controller = new $parsedUrl['controller_class'](array(
			'request' => new Rox_Request()
		));

		$controller->params = $parsedUrl;

		if ( method_exists('Rox_Controller', $parsedUrl['action_method']) ||
			!method_exists($controller, $parsedUrl['action_method']) ||
			strpos($parsedUrl['action_method'], '__') === 0 ||
			!is_callable(array($controller, $parsedUrl['action_method']))) {
			throw new Rox_Exception('Action does not exist or is not dispatchable', 404);
		}

		$controller->beforeFilter();
		call_user_func_array(array($controller, $parsedUrl['action_method']), $parsedUrl['params']);
		$controller->render();
		$controller->afterFilter();
	}

	/**
	 * Loads controller by class name
	 *
	 * @param string $name
	 * @throws Rox_Exception
	 */
	protected function _loadController($params) {
		$path = ROX_APP_PATH . "/controllers";
		if (isset($params['namespace']) && $params['namespace']) {
			$path .= "/{$params['namespace']}/{$params['controller_class']}.php";
		} else {
			$path .= "/{$params['controller_class']}.php";
		}

		if (!file_exists($path)) {
			throw new Rox_Exception('Missing controller file' . $path, 404);
		}

		require_once $path;
	}
}

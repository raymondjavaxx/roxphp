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
class Rox_Http_Dispatcher {

	/**
	 * Dispatches an HTTP request
	 *
	 * @param Rox_Http_Request $request
	 * @throws Rox_Exception
	 */
	public function dispatch($request) {
		Rox_Http_Request_Normalizer::normalize($request);

		$route = $request->getQuery('route', '/');

		$params = Rox_Router::parseUrl($route);
		if ($params === false) {
			throw new Rox_Exception('No route matches request', 404);
		}

		$this->_loadController($params);

		$response = new Rox_Http_Response;

		$controller = new $params['controller_class'](array(
			'request' => $request,
			'response' => $response
		));

		$controller->params = $params;

		if (!method_exists($controller, $params['action_method']) ||
			!is_callable(array($controller, $params['action_method']))) {
			throw new Rox_Exception('Action does not exist or is not dispatchable', 404);
		}

		$controller->beforeFilter();
		call_user_func_array(array($controller, $params['action_method']), $params['args']);
		$controller->render();
		$controller->afterFilter();

		$controller->response->render();
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

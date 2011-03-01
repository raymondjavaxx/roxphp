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

namespace rox\http;

use \rox\http\request\Normalizer;
use \rox\Router;

/**
 * Dispatcher
 *
 * @package Rox
 */
class Dispatcher {

	/**
	 * Dispatches an HTTP request
	 *
	 * @param \rox\http\Request $request
	 * @throws \rox\http\DispatcherException
	 */
	public function dispatch(Request $request) {
		Normalizer::normalize($request);

		$route = $request->getQuery('route', '/');

		$params = Router::parseUrl($route, $request);
		if ($params === false) {
			throw new DispatcherException('No route matches request', 404);
		}

		$this->_loadController($params);

		$response = new Response;

		$controller = new $params['controller_class'](array(
			'request' => $request,
			'response' => $response
		));

		$controller->params = $params;

		if (!method_exists($controller, $params['action_method']) ||
			!is_callable(array($controller, $params['action_method']))) {
			throw new DispatcherException('Action does not exist or is not dispatchable', 404);
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
	 * @throws \rox\http\DispatcherException
	 */
	protected function _loadController($params) {
		$path = ROX_APP_PATH . "/controllers";
		if (isset($params['namespace']) && $params['namespace']) {
			$path .= "/{$params['namespace']}/{$params['controller_class']}.php";
		} else {
			$path .= "/{$params['controller_class']}.php";
		}

		if (!file_exists($path)) {
			throw new DispatcherException('Missing controller file' . $path, 404);
		}

		require_once $path;
	}
}

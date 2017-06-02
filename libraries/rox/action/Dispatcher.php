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

namespace rox\action;

use \rox\Router;
use \rox\http\Request;
use \rox\http\Response;

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
	 * @return \rox\http\Response
	 * @throws \rox\http\DispatcherException
	 */
	public function dispatch(Request $request) {
		$route = $request->query->get('route', '/');

		$params = Router::parseUrl($route, $request);
		if ($params === false) {
			throw new DispatcherException('No route matches request', 404);
		}

		if (!class_exists($params['controller_class'])) {
			throw new DispatcherException("Missing controller class {$params['controller_class']}", 404);
		}

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

		return $controller->response;
	}

}

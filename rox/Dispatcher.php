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
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Dispatcher
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Dispatcher {

	/**
	 * Rox_Dispatcher::dispatch()
	 *
	 * @param mixed $url
	 * @throws Exception
	 */
	public function dispatch($url = null) {
		$parsedUrl = Rox_Router::parseUrl($url);

		$this->_loadController($parsedUrl['controller_class']);

		$controller = new $parsedUrl['controller_class'](array(
			'request' => new Rox_Request()
		));

		$controller->params = $parsedUrl;

		if ( method_exists('Rox_Controller', $parsedUrl['action_method']) ||
			!method_exists($controller, $parsedUrl['action_method']) ||
			strpos($parsedUrl['action_method'], '__') === 0 ||
			!is_callable(array($controller, $parsedUrl['action_method']))) {
			throw new Exception('Action does not exist or is not dispatchable', 404);
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
	 * @throws Exception
	 */
	protected function _loadController($name) {
		$filename = ROX_APP_PATH . DS . 'controllers' . DS . $name . '.php';
		if (!file_exists($filename)) {
			throw new Exception('Missing controller file', 404);
		}

		require_once $filename;
	}
}

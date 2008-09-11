<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * @see Request
 */
require_once ROX . 'Request.php';

/**
 * Dispatcher
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Dispatcher {

	/**
	 * Dispatcher::dispatch()
	 *
	 * @param mixed $url
	 * @throws Exception
	 */
	public function dispatch($url = null) {
		$parsedUrl = $this->_parseUrl(strtolower($url));

		$this->_loadController($parsedUrl['controller']);
		$Controller = new $parsedUrl['controller'];

		if ( method_exists('Controller', $parsedUrl['action']) ||
			!method_exists($Controller, $parsedUrl['action']) ||
			!is_callable(array($Controller, $parsedUrl['action']))) {
			throw new Exception('Action does not exist or is not dispatchable', 404);
		}

		$Controller->setRequest(new Request);
		$Controller->setAction($parsedUrl['action']);
		$Controller->setData(isset($_POST['d']) ? $_POST['d'] : array());

		call_user_func_array(array($Controller, $parsedUrl['action']), $parsedUrl['params']);
		$Controller->render();
	}

	/**
	 * Dispatcher::loadController()
	 *
	 * @param string $name
	 * @throws Exception
	 */
	protected function _loadController($name) {
		$fileName = CONTROLLERS . $name . '.php';
		if (!file_exists($fileName)) {
			throw new Exception('Missing controller file', 404);
		}

		require_once $fileName;
	}

	/**
	 * Dispatcher::_parseUrl()
	 * 
	 * @param string $url
	 * @return array
	 * @throws Exception
	 */
	protected function _parseUrl($url) {
		$parts = explode('/', $url);

		if (preg_match('/^[a-z_]+$/', $parts[0]) != 1) {
			throw new Exception('Ilegal controller name', 404);
		}

		$result = array(
			'controller' => str_replace(' ', '', ucwords(str_replace('_', ' ', $parts[0]))) . 'Controller',
			'action'     => isset($parts[1]) ? $parts[1] : 'index',
			'params'     => array_slice($parts, 2)
		);

		return $result;		
	}
}
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
 * @see Request
 */
require_once ROX . 'Request.php';

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
		$parsedUrl = $this->_parseUrl(strtolower($url));

		$this->_loadController($parsedUrl['controller']);
		$controller = new $parsedUrl['controller'];

		if ( method_exists('Controller', $parsedUrl['action']) ||
			!method_exists($controller, $parsedUrl['action']) ||
			strpos($parsedUrl['action'], '__') === 0 ||
			!is_callable(array($controller, $parsedUrl['action']))) {
			throw new Exception('Action does not exist or is not dispatchable', 404);
		}

		$controller->setRequest(new Request);
		$controller->setAction($parsedUrl['action']);

		call_user_func_array(array($controller, $parsedUrl['action']), $parsedUrl['params']);
		$controller->render();
	}

	/**
	 * Rox_Dispatcher::loadController()
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
	 * Rox_Dispatcher::_parseUrl()
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
			'controller' => Rox_Inflector::camelize($parts[0]).'Controller',
			'action'     => isset($parts[1]) ? Rox_Inflector::lowerCamelize($parts[1]).'Action' : 'indexAction',
			'params'     => array_slice($parts, 2)
		);

		return $result;		
	}
}

<?php
/**
 * Dispatcher
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Dispatcher extends Object {

  /**
   * Dispatcher::dispatch()
   *
   * @param mixed $url
   * @return
   */
	public function dispatch($url = null) {
		$url = strtolower($url);
		$parts = explode('/', $url);

		if (preg_match('/^[a-z_]+$/', $parts[0]) != 1) {
			throw new RoxException(404, 'Ilegal controller name');
		}

		if (!isset($parts[1])) {
			$parts[1] = 'index';
		}

		$this->loadController($parts[0]);

		$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $parts[0]))) . 'Controller';
		$controller = new $controllerName;

		if ( method_exists('Controller', $parts[1]) ||
			!method_exists($controller, $parts[1]) ||
			!is_callable(array($controller, $parts[1]))) {
			throw new RoxException(404, 'Action does not exist');
		}

		$controller->action = $parts[1];
		$controller->data = isset($_POST['d']) ? $_POST['d'] : array();

		call_user_func_array(array(&$controller, $parts[1]), array_slice($parts, 2));
		$controller->render();
	}

  /**
   * Dispatcher::loadController()
   *
   * @param mixed $name
   * @return
   */
	public function loadController($name) {
		$fileName = CONTROLLERS . DS . $name . '_controller.php';
		if (!file_exists($fileName)) {
			throw new RoxException(404, 'Missing controller file');
		}

		require_once($fileName);
	}
}
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
   * @throws Exception
   */
	public function dispatch($url = null) {
		$url = strtolower($url);
		$parts = explode('/', $url);

		if (preg_match('/^[a-z_]+$/', $parts[0]) != 1) {
			throw new Exception('Ilegal controller name', 404);
		}

		if (!isset($parts[1])) {
			$parts[1] = 'index';
		}

		$this->loadController($parts[0]);

		$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $parts[0]))) . 'Controller';
		$Controller = new $controllerName;

		if ( method_exists('Controller', $parts[1]) ||
			!method_exists($Controller, $parts[1]) ||
			!is_callable(array($Controller, $parts[1]))) {
			throw new Exception('Action does not exist', 404);
		}

		$Controller->setAction($parts[1]);
		$Controller->setData(isset($_POST['d']) ? $_POST['d'] : array());

		call_user_func_array(array(&$Controller, $parts[1]), array_slice($parts, 2));
		$Controller->render();
	}

  /**
   * Dispatcher::loadController()
   *
   * @param string $name
   * @throws Exception
   */
	public function loadController($name) {
		$fileName = CONTROLLERS . $name . '_controller.php';
		if (!file_exists($fileName)) {
			throw new Exception('Missing controller file', 404);
		}

		require_once($fileName);
	}
}
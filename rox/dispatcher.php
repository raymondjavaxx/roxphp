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
	function dispatch($url = null) {
		$parts = explode('/', $url);

		if (!isset($parts[1])) {
			$parts[1] = 'index';
		}

		$controllerName = $parts[0] . '_controller';
		$this->loadController($parts[0]);
		$controller = new $controllerName;

		if ( method_exists('Controller', $parts[1]) ||
			!method_exists($controller, $parts[1]) ||
			!is_callable(array($controller, $parts[1]))) {
			throw new RoxException(404);
		}

		$controller->action = $parts[1];
		$controller->data = isset($_POST['d']) ? $_POST['d'] : array();

		$controller->{$parts[1]}();
		$controller->render();
	}

  /**
   * Dispatcher::loadController()
   *
   * @param mixed $name
   * @return
   */
	function loadController($name) {
		$fileName = CONTROLLERS . DS . strtolower($name) . '_controller.php';
		if (!file_exists($fileName)) {
			throw new RoxException(404);
		}

		require_once($fileName);
	}
}
?>
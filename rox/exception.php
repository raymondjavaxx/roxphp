<?php
/**
 * RoxExceptionHandler
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
class RoxExceptionHandler {

  /**
   * RoxExceptionHandler::handleException()
   *
   * @param Exception $Exception
   */
	public static function handleException($Exception) {
		RoxExceptionHandler::render($Exception);
		exit;
	}

  /**
   * Renders the exception
   * 
   * @param Exception $Exception   
   */
	private static function render(&$Exception) {
		header("HTTP/1.0 500 Internal Server Error");

		$viewName = (string)$Exception->getCode();

		if (!file_exists(VIEWS . 'errors' . DS . $viewName . '.tpl')) {
			$viewName = 'unknown';
		}

		$data = array();
		$viewVars = array('exception' => $Exception);

		$View = new View($viewVars, $data);
		$View->render('errors', $viewName, 'exception');
	}
}

set_exception_handler(array('RoxExceptionHandler', 'handleException'));
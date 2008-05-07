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

		$viewVars = array(
			'code' => $Exception->getCode(),
			'message' => $Exception->getMessage(),
			'rox_page_title' => 'Error'
		);

		$viewName = (string)$viewVars['code'];

		if (!file_exists(VIEWS . 'errors' . DS . $viewName . '.tpl')) {
			$viewName = 'unknown';
		}

		$data = array();
		$View = new View($viewVars, $data);
		$View->render('errors', $viewName);
	}
}

set_exception_handler(array('RoxExceptionHandler', 'handleException'));
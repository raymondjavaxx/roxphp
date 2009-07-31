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
 * Exception Handler
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Exception_Handler {

	/**
	 * Exception handler
	 *
	 * @param Exception $exception
	 */
	public static function handle($exception) {
		if (ob_get_level() > 0) {
			ob_end_clean();
		}

		self::_render($exception);
		exit;
	}

	/**
	 * Renders the error page associated with the exception code
	 * 
	 * @param Exception $exception   
	 */
	private static function _render($exception) {
		header("HTTP/1.0 500 Internal Server Error");

		$viewName = (string)$exception->getCode();

		if (!file_exists(VIEWS . 'errors' . DS . $viewName . '.tpl')) {
			$viewName = 'unknown';
		}

		$view = new Rox_View(array('exception' => $exception));
		echo $view->render('errors', $viewName, 'exception');
	}
}

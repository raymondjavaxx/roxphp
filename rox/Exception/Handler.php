<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Exception Handler
 *
 * @package Rox
 */
class Rox_Exception_Handler {

	/**
	 * Exception handler
	 *
	 * @param Exception $exception
	 */
	public static function handle($exception) {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}

		self::_render($exception);
		exit;
	}

	/**
	 * Registers Rox_Exception_Handler class as exception handler
	 *
	 * @return void
	 */
	public static function register() {
		set_exception_handler(array('Rox_Exception_Handler', 'handle'));
	}

	/**
	 * Renders the error page associated with the exception code
	 * 
	 * @param Exception $exception   
	 */
	private static function _render($exception) {
		header("HTTP/1.0 500 Internal Server Error");

		$viewName = (string)$exception->getCode();

		if (!file_exists(ROX_APP_PATH . "/views/errors/{$viewName}.html.tpl")) {
			$viewName = 'unknown';
		}

		$view = new Rox_Template_View(array('exception' => $exception));
		echo $view->render('errors', $viewName, 'exception');
	}
}

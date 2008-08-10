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
 * RoxExceptionHandler
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class RoxExceptionHandler {

  /**
   * RoxExceptionHandler::handleException()
   *
   * @param Exception $Exception
   */
	public static function handle($Exception) {
		self::_render($Exception);
		exit;
	}

  /**
   * Renders the exception
   * 
   * @param Exception $Exception   
   */
	private static function _render(&$Exception) {
		header("HTTP/1.0 500 Internal Server Error");

		$viewName = (string)$Exception->getCode();

		if (!file_exists(VIEWS . 'errors' . DS . $viewName . '.tpl')) {
			$viewName = 'unknown';
		}

		$View = new View(array('exception' => $Exception));
		echo $View->render('errors', $viewName, 'exception');
	}
}

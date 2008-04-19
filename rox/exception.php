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
   * @param mixed $Exception
   */
	public static function handleException($Exception) {
		$Exception->render();
		exit;
	}
}

/**
 * RoxException
 */
class RoxException extends Exception {

	private $info = null;

  /**
   * Class constructor
   *
   * @param mixed $type
   * @param string $message
   */
	public function __construct($type = 'unknown', $message = 'Unknown exception') {
		$this->info = array(
			'type' => $type,
			'message' => $message,
			'rox_page_title' => 'Error',
		);
	}

  /**
   * Renders the exception
   */
	public function render() {
		// this header will be overwritten
		header("HTTP/1.0 500 Internal Server Error");

		$data = array();
		$View = new View($this->info, $data);
		$View->render('errors', $this->info['type']);
	}
}

set_exception_handler(array('RoxExceptionHandler', 'handleException'));
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
   * @return
   */
	static function handleException($Exception) {
		$Exception->render();
		exit;
	}
}

/**
 * RoxException
 */
class RoxException extends Exception {

	var $info = null;

  /**
   * RoxException::__construct()
   *
   * @param string $info
   * @return
   */
	function __construct($info = 'unknown') {
		if (!is_array($info)) {
			$info = array('type' => $info);
		}

		$info['rox_page_title'] = 'Error';

		$this->info = $info;
	}

  /**
   * RoxException::render()
   *
   * @return
   */
	function render() {
		// this header will be overwritten
		header("HTTP/1.0 500 Internal Server Error");

		$data = array();
		$View = new View($this->info, $data);
		$View->render('errors', $this->info['type']);
	}
}

set_exception_handler(array('RoxExceptionHandler', 'handleException'));

?>
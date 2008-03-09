<?php
/**
 * Controller
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
class Controller extends Object {

	var $name = '';

	var $pageTitle = 'RoxPHP';

	var $layout = 'default';

	var $viewVars = array();

	var $data = array();

	var $action = '';

	var $models = array();

  /**
   * Class constructor
   *
   * @return
   */
	function __construct() {
		foreach($this->models as $model) {
			require_once(MODELS . DS . $model . '.php');
			$this->{$model} = new $model;
		}
	}

  /**
   * Renders the current action
   *
   * @return
   */
	function render() {
		$this->set('rox_page_title', $this->pageTitle);
		$View = new View($this->viewVars, $this->data);
		$View->render($this->name, $this->action, $this->layout);
	}

  /**
   * Sets a view variable
   *
   * @param string $varName
   * @param mixed $value
   * @return
   */
	function set($varName, $value = null) {
		$this->viewVars[$varName] = $value;
	}

  /**
   * Returns true if the request is an Ajax request
   *
   * @return boolean
   */
	function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

  /**
   * Detects iPhone/iPod touch
   *
   * @return boolean
   */
	function isIPhone() {
		return strpos($_SERVER['HTTP_USER_AGENT'], 'iPho') !== FALSE;
	}
}
?>
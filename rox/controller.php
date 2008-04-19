<?php
/**
 * Controller
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Controller extends Object {

	public $name = '';

	protected $pageTitle = 'RoxPHP';

	public $layout = 'default';

	protected $viewVars = array();

	public $data = array();

	public $action = '';

	protected $models = array();

  /**
   * Class constructor
   *
   * @return
   */
	public function __construct() {
		foreach($this->models as $model) {
			Rox::loadModel($model);
			$this->{$model} = new $model;
		}
	}

  /**
   * Renders the current action
   *
   * @return
   */
	public function render() {
		$this->set('rox_page_title', $this->pageTitle);
		$View = new View($this->viewVars, $this->data);
		$View->render(strtolower($this->name), $this->action, $this->layout);
	}

  /**
   * Sets a view variable
   *
   * @param string $varName
   * @param mixed $value
   * @return
   */
	public function set($varName, $value = null) {
		$this->viewVars[$varName] = $value;
	}

  /**
   * Returns true if the request is an Ajax request
   *
   * @return boolean
   */
	public function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

  /**
   * Detects iPhone/iPod touch
   *
   * @return boolean
   */
	public function isIPhone() {
		return strpos($_SERVER['HTTP_USER_AGENT'], 'iPho') !== FALSE;
	}

  /**
   * Send redirects headers and exit
   *
   * @param string $url
   */
	public function redirect($url) {
		header('HTTP/1.1 301');
		header('Location: ' . Router::url($url));
		exit;
	}
}
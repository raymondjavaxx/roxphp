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

	protected $name = '';
	protected $layout = 'default';
	protected $pageTitle = 'RoxPHP';

	protected $action = '';
	protected $models = array();
	protected $data = array();

	protected $viewVars = array();

  /**
   * Class constructor
   */
	public function __construct() {
		foreach($this->models as $model) {
			$this->{$model} = Rox::getModel($model);
		}
	}

  /**
   * Data setter
   *
   * @param array $data 
   */   
	public function setData($data) {
		$this->data = $data;
	}

  /**
   * Renders the current action
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
   */
	public function set($varName, $value = null) {
		$this->viewVars[$varName] = $value;
	}

  /**
   * Page title setter
   *
   * @param $title
   */
	public function setTitle($title) {
		$this->pageTitle = $title;
	}

  /**
   * Action setter
   *
   * @param $action
   */
	public function setAction($action) {
		$this->action = $action;
	}

  /**
   * Layout setter
   *
   * @param $layout
   */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

  /**
   * Controller::isPost()
   *
   * @return boolean
   */
	protected function isPost() {
		if (!isset($_SERVER['REQUEST_METHOD'])) {
			return false;
		}

		return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0;
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
   * Send redirect headers and exit
   *
   * @param string $url
   */
	public function redirect($url) {
		header('HTTP/1.1 301');
		header('Location: ' . Router::url($url));
		exit;
	}
}
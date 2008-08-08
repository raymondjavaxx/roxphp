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
 * Controller
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Controller {

  /**
   * Controller name
   *
   * @var string
   */
	protected $name = '';

  /**
   * Page title
   *
   * @var string
   */
	protected $pageTitle = 'RoxPHP';

  /**
   * Layout name
   *
   * @var array  
   */
	protected $layout = 'default';

  /**
   * Current action
   *
   * @var string
   */
	protected $action = '';

  /**
   * Models to load automatically
   *
   * @var array
   */
	protected $models = array();

  /**
   * Posted data
   *
   * @var array  
   */
	protected $data = array();

  /**
   * Request object
   *
   * @var Request
   */
	protected $request;

  /**
   * View variables
   *
   * @var array  
   */
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
   * Controller::getData()
   *
   * @param string $model
   * @param string $field
   * @param mixed $default
   * @return mixed
   */
	public function getData($model, $field, $default = null) {
		if (isset($this->data[$model][$field])) {
			return $this->data[$model][$field];
		}
		return $default;
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
	 * Controller::setRequest()
	 * 
	 * @param Request $request
	 * @return void
	 */
	public function setRequest(Request $request) {
		$this->request = $request;
	}

  /**
   * Send redirect headers and exit
   *
   * @param string $url
   */
	protected function redirect($url) {
		header('HTTP/1.1 301');
		header('Location: ' . Router::url($url));
		exit;
	}

  /**
   * Redirects to referer
   *
   * @param string $default
   */
	protected function redirectToReferer($default = '/') {
		if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			$referer = $_SERVER['HTTP_REFERER'];
		} else {
			$referer = Router::url($default);
		}

		header('HTTP/1.1 301');
		header('Location: ' . $referer);
		exit;
	}
}
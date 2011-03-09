<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox;

/**
 * Controller
 *
 * @package Rox
 */
class Controller {

	/**
	 * Page title
	 *
	 * @var string
	 */
	public $pageTitle = 'RoxPHP';

	/**
	 * Layout name
	 *
	 * @var string
	 */
	public $layout = 'default';

	/**
	 * List of helpers to be automatically loaded when rendering
	 *
	 * @var array
	 */
	public $helpers = array();

	/**
	 * Request object
	 *
	 * @var \rox\http\Request
	 */
	public $request;

	/**
	 * Response object
	 *
	 * @var \rox\http\response
	 */
	public $response;

	/**
	 * Request params
	 *
	 * @var array
	 */
	public $params;

	/**
	 * View variables
	 *
	 * @var array  
	 */
	protected $_viewVars = array();

	/**
	 * Constructor
	 *
	 * @param array $config
	 * @return void
	 */
	public function __construct($config = array()) {
		if (isset($config['request'])) {
			$this->request = $config['request'];
		}

		if (isset($config['response'])) {
			$this->response = $config['response'];
		}

		$vars = get_class_vars('ApplicationController');
		$this->helpers = array_merge($vars['helpers'], $this->helpers);
	}

	/**
	 * Renders the current action
	 */
	public function render() {
		$this->set('rox_page_title', $this->pageTitle);

		foreach ($this->helpers as $helper) {
			$helperName = Inflector::lowerCamelize($helper);
			$this->set($helperName, Rox::getHelper($helper));
		}

		$viewPath = $this->params['controller'];
		if (!empty($this->params['namespace'])) {
			$simpleControllerName = substr($this->params['controller'], strlen($this->params['namespace']) + 1);
			$viewPath = $this->params['namespace'] . '/' . $simpleControllerName;
		}

		$viewName = $this->params['action'];

		$view = new \rox\template\View($this->_viewVars);
		$view->params = $this->params;

		$this->response->body = $view->render($viewPath, $viewName, $this->layout);
	}

	/**
	 * Sets a view variable
	 *
	 * @param string|array $varName
	 * @param mixed $value
	 */
	public function set($varName, $value = null) {
		if (is_array($varName)) {
			$this->_viewVars += $varName;
			return;
		}

		$this->_viewVars[$varName] = $value;
	}

	/**
	 * undocumented function
	 *
	 * @param string $type
	 * @param string $message 
	 */
	public function flash($type, $message) {
		if (!isset($_SESSION['flash'])) {
			$_SESSION['flash'] = array();
		}
		$_SESSION['flash'][$type] = $message;
	}

	/**
	 * Sends redirect headers and exit
	 *
	 * @param string $url
	 */
	protected function redirect($url, $options = array()) {
		$defaults = array('status' => 301);
		$options += $defaults;

		$location = preg_match('/^([a-z0-9]+):\/\//', $url) === 1 ? $url : Router::url($url);

		$this->response->status = $options['status'];
		$this->response->header('Location', $location);
		$this->response->render();
		exit;
	}

	/**
	 * Redirects to referer
	 *
	 * @param string $default
	 */
	protected function redirectToReferer($default = '/') {
		$url = empty($_SERVER['HTTP_REFERER']) ? $default : $_SERVER['HTTP_REFERER'];
		$this->redirect($url);
	}

	// ------------------------------------------------
	//  Callbacks
	// ------------------------------------------------

	/**
	 * Before-filter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
	}

	/**
	 * After-filter callback
	 *
	 * @return void
	 */
	public function afterFilter() {
	}
}

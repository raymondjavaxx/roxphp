<?php
/**
 * RoxPHP
 *
 * Copyright (c) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Controller
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Controller {

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
	protected $layout = 'default';

	/**
	 * undocumented variable
	 *
	 * @var array
	 */
	public $helpers = array('Html', 'Form');

	/**
	 * Request object
	 *
	 * @var Rox_Request
	 */
	public $request;

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
	 * Class constructor
	 */
	public function __construct($config = array()) {
		$defaults = array(
			'request' => null
		);

		$config += $defaults;

		if (!empty($config['request'])) {
			$this->request = $config['request'];
		}

		$vars = get_class_vars('ApplicationController');
		$this->helpers = array_merge($vars["helpers"], $this->helpers);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function beforeFilter() {
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function afterFilter() {
	}

	/**
	 * Renders the current action
	 */
	public function render() {
		$this->set('rox_page_title', $this->pageTitle);

		foreach ($this->helpers as $helper) {
			$helperName = Rox_Inflector::lowerCamelize($helper);
			$this->set($helperName, Rox::getHelper($helperName));
		}

		$viewPath = $this->params['controller'];
		$viewName = $this->params['action'];

		$view = new Rox_View($this->_viewVars);
		echo $view->render($viewPath, $viewName, $this->layout);
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
	 * Page title setter
	 *
	 * @param $title
	 */
	public function setPageTitle($title) {
		$this->_pageTitle = $title;
	}

	/**
	 * Send redirect headers and exit
	 *
	 * @param string $url
	 */
	protected function redirect($url) {
		header('HTTP/1.1 301');
		header('Location: ' . Rox_Router::url($url));
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
			$referer = Rox_Router::url($default);
		}

		header('HTTP/1.1 301');
		header('Location: ' . $referer);
		exit;
	}
}

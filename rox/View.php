<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
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
 *  View class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_View {

	public $params = array('extension' => 'html');

	protected $_vars = array();

	protected $_view;
	protected $_layout;

	protected $_layoutsPath;
	protected $_viewsPath;

	/**
	 * Class Constructor
	 *
	 * @param array $vars
	 */
	public function __construct($vars = array()) {
		$this->_vars = $vars;
		$this->_viewsPath = ROX_APP_PATH . DS . 'views';
		$this->_layoutsPath = $this->_viewsPath . DS . 'layouts';
	}

	/**
	 * Renders a view + layout
	 *
	 * @param string $path
	 * @param string $name
	 * @param string $layout
	 * @return string
	 */
	public function render($path, $name, $layout = 'default') {
		$this->_view = $this->_viewsPath . DS . $path . DS . $name . '.' . $this->params['extension'] . '.tpl';
		if (!file_exists($this->_view)) {
			throw new Exception("View doesn't exist", 404);
		}

		$this->_layout = $this->_layoutsPath . DS . $layout . '.' . $this->params['extension'] . '.tpl';
		if (!file_exists($this->_layout)) {
			throw new Exception("Layout doesn't exist", 404);
		}

		return $this->_render();
	}

	/**
	 * Backend for Rox_View::render()
	 *
	 * @return string
	 */
	private function _render() {
		extract($this->_vars, EXTR_SKIP);

		ob_start();
		require $this->_view;
		$rox_layout_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		require $this->_layout;
		return ob_get_clean();
	}

	/**
	 * undocumented function
	 *
	 * @param string $name 
	 * @return string
	 */
	public function element($name) {
		extract($this->_vars, EXTR_SKIP);

		ob_start();
		include $this->_viewsPath . DS . 'elements' . DS . $name . '.tpl';
		return ob_get_clean();
	}

	/**
	 * undocumented function
	 *
	 * @return array
	 */
	public function getFlashMessages() {
		$messages = array();
		if (isset($_SESSION['flash'])) {
			$messages = $_SESSION['flash'];
			unset($_SESSION['flash']);
		}
		return $messages;
	}
}

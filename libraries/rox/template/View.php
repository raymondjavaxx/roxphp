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

namespace rox\template;
use rox\Exception;
use rox\Rox;

/**
 *  View class
 *
 * @package Rox
 */
class View {

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
		$this->_viewsPath = ROX_APP_PATH . '/views';
		$this->_layoutsPath = $this->_viewsPath . '/layouts';
	}

	public function __get($attribute) {
		$this->{$attribute} = Rox::getHelper($attribute);
		return $this->{$attribute};
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
		$this->_view = "{$this->_viewsPath}/{$path}/{$name}.{$this->params['extension']}.tpl";
		if (!file_exists($this->_view)) {
			throw new Exception("View doesn't exist", 404);
		}

		$this->_layout = "{$this->_layoutsPath}/{$layout}.{$this->params['extension']}.tpl";
		if (!file_exists($this->_layout)) {
			throw new Exception("Layout doesn't exist", 404);
		}

		return $this->_render();
	}

	/**
	 * Backend for \rox\template\View::render()
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
	 * @param array $vars
	 * @return string
	 */
	public function element($name, $vars = array()) {
		extract($vars + $this->_vars, EXTR_SKIP);

		ob_start();
		include "{$this->_viewsPath}/elements/{$name}.tpl";
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

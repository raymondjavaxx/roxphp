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

	protected $_vars = array();

	/**
	 * Class Constructor
	 *
	 * @param array $vars
	 */
	public function __construct($vars = array()) {
		$this->_vars = $vars;
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
		extract($this->_vars, EXTR_SKIP);

		ob_start();
		include VIEWS.$path.DS.$name.'.tpl';
		$rox_layout_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		include LAYOUTS.$layout.'.tpl';
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

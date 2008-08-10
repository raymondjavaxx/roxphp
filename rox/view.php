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
 *  View
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class View {

	protected $_vars = array();

	/**
	 * Class Constructor
	 *
	 * @param array $vars
	 * @param array $data
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
		//load basic helpers
		require(ROX . 'helpers' . DS . 'html.php');
		require(ROX . 'helpers' . DS . 'form.php');

		$html = new HtmlHelper;
		$form = new FormHelper;

		extract($this->_vars, EXTR_SKIP);

		ob_start();
		include VIEWS . $path . DS . $name . '.tpl';
		$rox_layout_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		include LAYOUTS . $layout . '.tpl';
		return ob_get_clean();
	}
}
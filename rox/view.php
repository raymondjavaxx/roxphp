<?php
/**
 *  View
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
class View extends Object {

	var $vars = array();
	var $data = array();

  /**
   * Constructor
   *
   * @param array $vars
   * @param array $data
   */
	function __construct(&$vars, &$data) {
		$this->vars = $vars;
		$this->data = $data;
	}

  /**
   * Renders a view + layout
   *
   * @param string $path
   * @param string $name
   * @param string $layout
   */
	function render($path, $name, $layout = 'default') {
		//load basic helpers
		require(ROX . DS . 'helpers' . DS . 'html.php');
		require(ROX . DS . 'helpers' . DS . 'form.php');

		$html = new HtmlHelper;
		$form = new FormHelper($this->data);

		extract($this->vars, EXTR_SKIP);

		ob_start();
		include(VIEWS . DS . $path . DS . $name . '.tpl');
		$rox_layout_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		include(LAYOUTS . DS . $layout . '.tpl');
		ob_end_flush();
	}
}
?>
<?php
/**
 * HtmlHelper
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
class FormHelper extends Object {

	var $currentModel = null;

	var $data = array();

  /**
   * FormHelper::__construct()
   *
   * @param array $data
   * @return
   */
	function __construct(&$data = array()) {
		$this->data = $data;
	}

  /**
   * FormHelper::create()
   *
   * @param string $model
   * @param string $action
   * @param string $method
   * @return string
   */
	function create($model, $action, $method = 'post') {
		$this->currentModel = strtolower($model);
		$formTag = '<form action="%s" method="%s">';
		return sprintf($formTag, Router::url($action), $method); 
	}

  /**
   * FormHelper::input()
   *
   * @param string $name
   * @return string
   */
	function input($name) {
		$id = $this->currentModel . '_' . $name;
		$fieldName = "d[{$this->currentModel}][{$name}]";

		$div = '<div class="input">%s</div>';
		$input = '<input type="%s" name="%s" id="%s" value="%s" />';

		if (isset($this->data[$this->currentModel][$name])) {
			$value = htmlspecialchars($this->data[$this->currentModel][$name]);
		} else {
			$value = null;
		}

		$output = $this->label(ucwords($name));
		$output .= sprintf($input, 'text', $fieldName, $id, $value);

		return sprintf($div, $output);
	}

  /**
   * FormHelper::submit()
   *
   * @param string $text
   * @return string
   */
	function submit($text = 'Submit') {
		return '<div class="submit"><input type="submit" name="submit" value="' . $text . '" /></div>';
	}

  /**
   * FormHelper::label()
   *
   * @param string $text
   * @return
   */
	function label($text) {
		return '<label>'.$text.'</label>';
	}

  /**
   * FormHelper::end()
   *
   * @return string
   */
	function end() {
		return '</form>';
	}
}
?>
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

	private $currentModel = null;

	private $data = array();

  /**
   * FormHelper::__construct()
   *
   * @param array $data
   */
	public function __construct(&$data = array()) {
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
	public function create($model, $action, $method = 'post') {
		$this->currentModel = $model;
		$formTag = sprintf(
			'<form action="%s" method="%s">',
			Router::url($action),
			$method
		);
		return $formTag;
	}

  /**
   * FormHelper::input()
   *
   * @param string $name
   * @return string
   */
	public function input($name, $type = null, $value = null, $label = null) {
		$fieldName = 'd[' . $this->currentModel . '][' . $name . ']';

		if (is_null($value) && isset($this->data[$this->currentModel][$name])) {
			$value = $this->data[$this->currentModel][$name];
		}

		$output = $this->label(empty($label) ? ucwords($name) : $label);
		$output .= sprintf(
			'<input type="%s" name="%s" id="%s" value="%s" />',
			$type,
			$fieldName,
			$this->currentModel . '_' . $name . '_input',
			htmlspecialchars($value)
		);

		return sprintf('<div class="input">%s</div>', $output);
	}

  /**
   * FormHelper::submit()
   *
   * @param string $text
   * @return string
   */
	public function submit($text = 'Submit') {
		return '<div class="submit"><input type="submit" name="submit" value="' . $text . '" /></div>';
	}

  /**
   * FormHelper::label()
   *
   * @param string $text
   * @return string
   */
	public function label($text) {
		return '<label>'.$text.'</label>';
	}

  /**
   * FormHelper::end()
   *
   * @return string
   */
	public function end() {
		return '</form>';
	}
}
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
 * HtmlHelper
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Helper_Form {

	private $_currentModel = null;

	private $_data = array();

  /**
   * Rox_Helper_Form::__construct()
   *
   * @param array $data
   */
	public function __construct() {
		$this->_data = $_POST;
	}

  /**
   * Rox_Helper_Form::create()
   *
   * @param string $model
   * @param string $action
   * @param string $method
   * @return string
   */
	public function create($model, $action, $method = 'post') {
		$this->_currentModel = $model;
		$formTag = sprintf(
			'<form action="%s" method="%s">',
			Router::url($action),
			$method
		);
		return $formTag;
	}

  /**
   * Rox_Helper_Form::input()
   *
   * @param string $name
   * @return string
   */
	public function input($name, $type = null, $value = null, $label = null) {
		$fieldName = 'd[' . $this->_currentModel . '][' . $name . ']';

		if (is_null($value) && isset($this->_data[$this->_currentModel][$name])) {
			$value = $this->_data[$this->_currentModel][$name];
		}

		$output = $this->label(empty($label) ? ucwords($name) : $label);
		$output .= sprintf(
			'<input type="%s" name="%s" id="%s" value="%s" />',
			$type,
			$fieldName,
			$this->_currentModel . '_' . $name . '_input',
			htmlspecialchars($value)
		);

		return sprintf('<div class="input">%s</div>', $output);
	}

  /**
   * Rox_Helper_Form::submit()
   *
   * @param string $text
   * @return string
   */
	public function submit($text = 'Submit') {
		return '<div class="submit"><input type="submit" name="submit" value="' . $text . '" /></div>';
	}

  /**
   * Rox_Helper_Form::label()
   *
   * @param string $text
   * @return string
   */
	public function label($text) {
		return '<label>'.$text.'</label>';
	}

  /**
   * Rox_Helper_Form::end()
   *
   * @return string
   */
	public function end() {
		return '</form>';
	}
}

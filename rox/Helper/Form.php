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

	protected $_currentModel;

	protected $_data;

	protected $_validationErrors = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->_data = $_POST;
	}

	/**
	 * Rox_Helper_Form::forModel()
	 *
	 * @param Rox_ActiveRecord|string $model 
	 * @param array $options 
	 * @return string
	 */
	public function forModel($model, $options = array()) {
		if (is_string($model)) {
			$controller = Rox_Inflector::underscore($model);
			$controller = Rox_Inflector::pluralize($controller);
			$action = '/'.$controller.'/add';
			return $this->create($model, $action);
		}

		if (is_object($model) && ($model instanceof  Rox_ActiveRecord)) {
			$modelClass = get_class($model);
			$modelName = Rox_Inflector::underscore($modelClass);
			$controller = Rox_Inflector::pluralize($modelName);

			if ($model->getId() === null) {
				$action = '/'.$controller.'/add';
			} else {
				$action = '/'.$controller.'/edit/'.$model->getId();
			}

			$this->_data = array_merge($this->_data, array($modelName => $model->getData()));
			$this->_validationErrors[$modelName] = $model->getValidationErrors();

			return $this->create($modelName, $action);
		}

		throw new Exception('Invalid model param');
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
		$this->_currentModel = Rox_Inflector::underscore($model);
		$formTag = sprintf('<form action="%s" method="%s">', Rox_Router::url($action), $method);
		return $formTag;
	}

	/**
	 * Generates a form input element
	 *
	 * @param string $name
	 * @param array $options
	 * @return string
	 */
	public function input($name, $options = array()) {
		$defaultOptions = array(
			'type'  => 'text',
			'label' => null,
			'value' => null
		);

		$options = array_merge($defaultOptions, $options);

		if ($options['label'] === null) {
			$options['label'] = ucwords(str_replace('_', ' ', $name));
		}

		if ($options['value'] === null && isset($this->_data[$this->_currentModel][$name])) {
			$options['value'] = $this->_data[$this->_currentModel][$name];
		}

		$elementName = $this->_currentModel . '[' . $name . ']';
		$elementId = str_replace('_', '-', $this->_currentModel . '-' . $name . '-input');

		$output = $this->label($options['label'], $elementId);

		if ($options['type'] == 'textarea') {
			$output .= sprintf(
				'<textarea name="%s" id="%s">%s</textarea>',
				$elementName,
				$elementId,
				htmlspecialchars($options['value'])
			);
		} else {
			$output .= sprintf(
				'<input type="%s" name="%s" id="%s" value="%s" />',
				$options['type'],
				$elementName,
				$elementId,
				htmlspecialchars($options['value'])
			);
		}

		if (isset($this->_validationErrors[$this->_currentModel][$name])) {
			$output .= sprintf('<div class="error">%s</div>',
				htmlspecialchars($this->_validationErrors[$this->_currentModel][$name]));
		}

		return sprintf('<div class="input">%s</div>', $output);
	}

	/**
	 * Generates a textarea form element
	 *
	 * @param string $name 
	 * @param array $options 
	 * @return string
	 */
	public function textarea($name, $options = array()) {
		$options = array_merge($options, array('type' => 'textarea'));
		return $this->input($name, $options);
	}

	/**
	 * undocumented function
	 *
	 * @param string $name 
	 * @param array $options 
	 * @return string
	 */
	public function hidden($name, $options = array()) {
		$options['type'] = 'hidden';

		if (!isset($options['name'])) {
			$options['name'] = $this->_makeFieldName($name);
		}

		if (!isset($options['id'])) {
			$options['id'] = $this->_makeFieldId($name);
		}
	
		if (!isset($options['value'])) {
			$options['value'] = $this->_valueForField($name);
		}

		return $this->_makeSelfClosingTag('input', $options);
	}

	/**
	 * Generates a submit form element
	 *
	 * @param string $text
	 * @return string
	 */
	public function submit($text = 'Submit', $options = array()) {
		return sprintf('<div class="submit"><input type="submit" name="submit" value="%s" /></div>', $text);
	}

	/**
	 * Generates a label html tag
	 *
	 * @param string $text
	 * @return string
	 */
	public function label($text, $for = null) {
		return sprintf('<label for="%s">%s</label>', $for, htmlspecialchars($text));
	}

	/**
	 * Rox_Helper_Form::end()
	 *
	 * @return string
	 */
	public function end() {
		return '</form>';
	}

	protected function _valueForField($name) {
		return isset($this->_data[$this->_currentModel][$name])
			? $this->_data[$this->_currentModel][$name] : null;
	}

	protected function _makeFieldId($name) {
		return str_replace('_', '-', $this->_currentModel . '-' . $name . '-input');
	}

	protected function _makeFieldName($name) {
		return $this->_currentModel.'['.$name.']';
	}

	protected function _makeSelfClosingTag($name, $attributes = array()) {
		return sprintf('<%s%s/>', $name, $this->_makeAttributes($attributes));
	}

	protected function _makeAttributes($attributes) {
		$output = array();
		foreach ($attributes as $k => $v) {
			$output[] = $k . '="' . $v . '"';
		}

		return ' ' . implode(' ', $output);
	}
}

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
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Form Helper
 *
 * @package Rox
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Helper_Form {

	/**
	 * Holds the name of the current model
	 *
	 * @var string
	 */
	protected $_currentModel;

	/**
	 * undocumented variable
	 *
	 * @var array
	 */
	protected $_data;

	/**
	 * Validation errors
	 *
	 * @var array
	 */
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
		$options = array_merge(array('action' => null), $options);

		if (is_string($model)) {
			if ($options['action'] === null) {
				$controller = Rox_Inflector::underscore($model);
				$controller = Rox_Inflector::pluralize($controller);
				$options['action'] = '/'.$controller.'/add';
			}
			return $this->create($model, $options['action']);
		}

		if (!is_object($model) || !($model instanceof  Rox_ActiveRecord)) {
			throw new Exception('Invalid model param');
		}

		$modelClass = get_class($model);
		$modelName = Rox_Inflector::underscore($modelClass);
		if ($options['action'] === null) {
			$controller = Rox_Inflector::pluralize($modelName);
			$options['action'] = ($model->getId() === null) ? '/'.$controller.'/add' :
				'/'.$controller.'/edit/'.$model->getId();
		}

		$this->_data = array_merge($this->_data, array($modelName => $model->getData()));
		$this->_validationErrors[$modelName] = $model->getValidationErrors();

		return $this->create($modelName, $options['action'], $options);
	}

	/**
	 * Creates a form opening tag
	 *
	 * @param string $model
	 * @param string $action
	 * @param array $attributes
	 * @return string
	 */
	public function create($model, $action, $attributes = array()) {
		$attributes['action'] = Rox_Router::url($action);
		$attributes = array_merge(array(
			'method' => 'post',
		), $attributes);

		$this->_currentModel = Rox_Inflector::underscore($model);
		$formTag = sprintf('<form%s>', $this->_makeAttributes($attributes));
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
		$options = array_merge(array('type'  => 'text', 'label' => null, 'value' => null), $options);

		if ($options['label'] === null) {
			$options['label'] = ucwords(str_replace('_', ' ', $name));
		}

		$attributes = $this->_normalizeAttributes($name, array('value' => $options['value']));

		$output = array();

		switch ($options['type']) {
			case 'text':
				$output[] = $this->label($options['label'], $attributes['id']);
				$output[] = $this->text($name, $attributes);
				break;

			case 'password':
				$output[] = $this->label($options['label'], $attributes['id']);
				$output[] = $this->password($name, $attributes);
				break;

			case 'file':
				$output[] = $this->label($options['label'], $attributes['id']);
				$output[] = $this->file($name, $attributes);
				break;

			case 'select':
				$output[] = $this->label($options['label'], $attributes['id']);
				$selectOptions = isset($options['options']) ? $options['options'] : array();
				unset($options['options']);
				$output[] = $this->select($name, $selectOptions, $options);
				break;

			case 'textarea':
				$output[] = $this->label($options['label'], $attributes['id']);
				$output[] = $this->textarea($name, $attributes);
				break;

			case 'checkbox':
				$output[] = $this->checkbox($name, $attributes);
				$output[] = $this->label($options['label'], $attributes['id']);
				break;

			default:
				throw new Exception("Unknown type '{$options['type']}'");
				break;
		}

		if (isset($this->_validationErrors[$this->_currentModel][$name])) {
			$output[] = sprintf('<div class="error">%s</div>',
				htmlspecialchars($this->_validationErrors[$this->_currentModel][$name]));
		}

		return sprintf('<div class="input %s-input">%s</div>', $options['type'], implode('', $output));
	}

	/**
	 * Creates a text input field
	 *
	 * @param string $attributes
	 * @return void
	 */
	public function text($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'text'));
		return $this->_makeSelfClosingTag('input', $attributes);
	}

	/**
	 * Creates a password input field
	 *
	 * @param string $name 
	 * @param array $attributes 
	 * @return string
	 */
	public function password($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'password'));
		return $this->_makeSelfClosingTag('input', $attributes);
	}

	/**
	 * Generates a file input field
	 *
	 * @param string $name
	 * @param array $attributes
	 * @return string
	 */
	public function file($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'file'));
		return $this->_makeSelfClosingTag('input', $attributes);
	}

	/**
	 * Generates a textarea form element
	 *
	 * @param string $name 
	 * @param array $attributes 
	 * @return string
	 */
	public function textarea($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes);
		$value = $attributes['value'];
		unset($attributes['value']);

		$output = sprintf('<textarea%s>%s</textarea>',
			$this->_makeAttributes($attributes), htmlspecialchars($value));

		return $output;
	}

	/**
	 * Renders a hidden input field
	 *
	 * @param string $name
	 * @param array $attributes
	 * @return string
	 */
	public function hidden($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'hidden'));
		return $this->_makeSelfClosingTag('input', $attributes);
	}

	public function checkbox($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'checkbox'));
		if ($attributes['value']) {
			$attributes['checked'] = 'checked';
		}

		$attributes['value'] = 1;

		$output = array();
		$output[] = $this->_makeSelfClosingTag('input', array(
			//'id'    => $attributes['id'] . '_',
			'name'  => $attributes['name'],
			'value' => 0,
			'type'  => 'hidden'
		));

		$output[] = $this->_makeSelfClosingTag('input', $attributes);
		return implode('', $output);
	}

	/**
	 * undocumented function
	 *
	 * @param string $name 
	 * @param array $optionTags 
	 * @param array $options 
	 * @return string
	 */
	public function select($name, $optionTags, $options = array()) {
		$options = array_merge(array(
			'value' => null,
			'attributes' => array(),
			'multiple'  => false
		), $options);

		if ($options['value'] === null) {
			$options['value'] = $this->_valueForField($name);
		}

		if (!isset($options['attributes']['name'])) {
			$options['attributes']['name'] = $this->_makeFieldName($name);
			if ($options['multiple']) {
				$options['attributes']['name'] .= '[]';
			}
		}

		if (!isset($options['attributes']['id'])) {
			$options['attributes']['id'] = $this->_makeFieldId($name);
		}

		if ($options['multiple']) {
			$options['attributes']['multiple'] = 'multiple';
		}

		$output = array();
		$output[] = sprintf('<select%s>', $this->_makeAttributes($options['attributes']));
		foreach ($optionTags as $value => $label) {
			$isSelected = ($value == $options['value']) ||
				($options['multiple'] && is_array($options['value']) && in_array($value, $options['value']));
			if ($isSelected) {
				$output[] = sprintf('<option selected="selected" value="%s">%s</option>', $value, $label);
			} else {
				$output[] = sprintf('<option value="%s">%s</option>',
					htmlspecialchars($value), htmlspecialchars($label));
			}
		}
		$output[] = '</select>';

		return implode('', $output);
	}


	protected function _normalizeAttributes($fieldName, $attributes, $forcedAttributes = array()) {
		$attributes = array_merge($attributes, $forcedAttributes);
		if (!isset($attributes['name'])) {
			$attributes['name'] = $this->_makeFieldName($fieldName);
		}

		if (!isset($attributes['id'])) {
			$attributes['id'] = $this->_makeFieldId($fieldName);
		}
	
		if (!isset($attributes['value'])) {
			$attributes['value'] = $this->_valueForField($fieldName);
		}

		return $attributes;
	}

	/**
	 * Generates a submit form element wrapped in a div element
	 *
	 * @param string $text
	 * @return string
	 */
	public function submit($text = 'Submit') {
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

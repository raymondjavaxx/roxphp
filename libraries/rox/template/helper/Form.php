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

namespace rox\template\helper;

use \rox\Router;
use \rox\Inflector;
use \rox\Exception;
use \rox\ActiveModel;

/**
 * Form Helper
 *
 * @package Rox
 */
class Form extends \rox\template\Helper {

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
	 * Options
	 *
	 * @var array
	 */
	protected $_options = array('is_child' => false);

	/**
	 * Constructor
	 */
	public function __construct($options = array()) {
		$this->_data = $_POST;
		$this->_options = array_merge($this->_options, $options);
	}

	/**
	 * \rox\template\helper\Form::forModel()
	 *
	 * @param \rox\ActiveRecord|string $model 
	 * @param array $options 
	 * @return string
	 */
	public function forModel($model, $options = array()) {
		$options = array_merge(array('action' => null), $options);
		$this->setModel($model);

		$method = (is_string($model) || $model->getId() === null) ? 'POST' : 'PUT';

		if ($options['action'] === null) {
			$controller = Inflector::pluralize($this->_currentModel);
			$options['action'] = (is_string($model) || $model->getId() === null) ? "/{$controller}" :
				'/' . $controller . '/' . $model->getId();
		}

		$result = array();
		$result[] = $this->create($options['action'], $options);
		$result[] = sprintf('<input type="hidden" name="_method" value="%s">', $method);
		return implode('', $result);
	}

	public function setModel($model) {
		$validModel = is_string($model) || (is_object($model) && $model instanceof  ActiveModel);
		if (!$validModel) {
			throw new Exception('Model should be string or a \rox\ActiveModel');
		}

		$modelName = Inflector::underscore(is_object($model) ? get_class($model) : $model);
		$this->_currentModel = $modelName;

		if (is_object($model)) {
			$this->_data = array_merge($this->_data, array($modelName => $model->getData()));
			$this->_validationErrors[$modelName] = $model->getValidationErrors();
		}
	}

	public function fieldsFor($model) {
		$class = get_class($this);
		$helperInstance = new $class(array('is_child' => true));
		$helperInstance->setModel($model);
		return $helperInstance;
	}

	/**
	 * Creates a form opening tag
	 *
	 * @param string $model
	 * @param string $action
	 * @param array $attributes
	 * @return string
	 */
	public function create($action, $attributes = array()) {
		$attributes['action'] = Router::url($action);
		$attributes = array_merge(array('method' => 'post'), $attributes);
		return sprintf('<form%s>', $this->_attributes($attributes));
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
			'value' => null,
			'attributes' => array()
		);

		$options = array_merge($defaultOptions, $options);
		if (!isset($options['attributes']['value'])) {
			$options['attributes']['value'] = $options['value'];
		}

		$attributes = $this->_normalizeAttributes($name, $options['attributes']);

		if ($options['label'] === null) {
			$options['label'] = ucwords(str_replace('_', ' ', $name));
		}

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

		$divClasses = array('input', sprintf('%s-input', $options['type']));

		if (isset($options['hint'])) {
			$output[] = sprintf('<span class="hint">%s</span>', $options['hint']);
		}

		if (isset($this->_validationErrors[$this->_currentModel][$name])) {
			$divClasses[] = 'field-with-errors';
			$output[] = sprintf('<div class="error">%s</div>',
				htmlspecialchars($this->_validationErrors[$this->_currentModel][$name]));
		}

		return sprintf('<div class="%s">%s</div>', implode(' ', $divClasses), implode('', $output));
	}

	/**
	 * Creates a text input field
	 *
	 * @param string $attributes
	 * @return void
	 */
	public function text($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'text'));
		return $this->_selfClosingTag('input', $attributes);
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
		return $this->_selfClosingTag('input', $attributes);
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
		unset($attributes['value']);
		return $this->_selfClosingTag('input', $attributes);
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
			$this->_attributes($attributes), htmlspecialchars($value));

		return $output;
	}

	public function error($field) {
		if (!isset($this->_validationErrors[$this->_currentModel][$field])) {
			return null;
		}

		return sprintf('<div class="error">%s</div>',
			htmlspecialchars($this->_validationErrors[$this->_currentModel][$field]));
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
		return $this->_selfClosingTag('input', $attributes);
	}

	public function checkbox($name, $attributes = array()) {
		$attributes = $this->_normalizeAttributes($name, $attributes, array('type' => 'checkbox'));
		if ($attributes['value']) {
			$attributes['checked'] = 'checked';
		}

		$attributes['value'] = 1;

		$output = array();
		$output[] = $this->_selfClosingTag('input', array(
			//'id'    => $attributes['id'] . '_',
			'name'  => $attributes['name'],
			'value' => 0,
			'type'  => 'hidden'
		));

		$output[] = $this->_selfClosingTag('input', $attributes);
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
			'multiple'  => false,
			'empty' => false
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
		$output[] = sprintf('<select%s>', $this->_attributes($options['attributes']));

		if ($options['empty'] !== false) {
			$output[] = sprintf('<option value="">%s</option>', htmlspecialchars($options['empty']));
		}

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
		return sprintf('<label for="%s">%s</label>', $for, $text);
	}

	/**
	 * \rox\template\helper\Form::end()
	 *
	 * @return string
	 */
	public function end() {
		return $this->_options['is_child'] ? null : '</form>';
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
}

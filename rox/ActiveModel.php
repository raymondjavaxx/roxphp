<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Rox_ActiveModel class
 *
 * @package Rox
 */
abstract class Rox_ActiveModel {

	/**
	 * Primary key
	 *
	 * @var string
	 */
	protected $_primaryKey = 'id';

	/**
	 * The name of DataSource used by this model
	 *
	 * @see Rox_ConnectionManager::getDataSource()
	 * @var string
	 */
	protected $_dataSourceName = 'default';

	/**
	 * Object data
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * List of attributes that are protected from mass assignment
	 *
	 * @var array
	 */
	protected $_protectedAttributes = array('id');

	/**
	 * Array of modified attributes
	 *
	 * @var array
	 */
	protected $_modifiedAttributes = array();

	/**
	 * Used to check if record is new
	 *
	 * @var boolean
	 */
	protected $_newRecord = true;


	/**
	 * Validation errors
	 *
	 * @var Rox_ActiveModel_ErrorCollection
	 */
	protected $_errors;

    protected $_behaviors;

	/**
	 * Constructor
	 *
	 * @param array $attributes
	 * @return void
	 */
	public function __construct(array $attributes = null) {
		if ($attributes !== null) {
			$this->setData($attributes);
		}
	}

	public static function model($class = __CLASS__) {
		static $instances = array();
		if (!isset($instances[$class])) {
			$instances[$class] = new $class;
		}
		return $instances[$class];
	}

	/**
	 * Sets the record ID
	 *
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->setData($this->_primaryKey, $id);
	}

	/**
	 * Returns the record ID
	 *
	 * @return mixed $id
	 */
	public function getId() {
		return $this->getData($this->_primaryKey);
	}

	/**
	 * Flags a given attribute as "modified"
	 *
	 * @param string $attribute
	 * @return void
	 */
	protected function _flagAttributeAsModified($attribute) {
		if (!in_array($attribute, $this->_modifiedAttributes)) {
			$this->_modifiedAttributes[] = $attribute;
		}
	}

	/**
	 * Resets the modified attributes list
	 *
	 * @return void
	 */
	protected function _resetModifiedAttributesFlags() {
		$this->_modifiedAttributes = array();
	}

	/**
	 * Set data
	 *
	 * @param string|array $attribute
	 * @param mixed $value
	 */
	public function setData($attribute, $value = null) {
		if (is_array($attribute)) {
			foreach ($attribute as $k => $v) {
				if (in_array($k, $this->_protectedAttributes)) {
					unset($attribute[$k]);
				}
			}

			$this->_data = array_merge($this->_data, $attribute);
			$attributeNames = array_keys($attribute);
			array_walk($attributeNames, array($this, '_flagAttributeAsModified'));
		} else {
			$this->_data[$attribute] = $value;
			$this->_flagAttributeAsModified($attribute);
		}
	}

	/**
	 * Returns the value of a given attribute.
	 *
	 * @param string $attribute
	 * @return mixed
	 */
	public function getData($attribute = null) {
		if ($attribute === null) {
			return $this->_data;
		}

		return array_key_exists($attribute, $this->_data) ? $this->_data[$attribute] : null;
	}

	/**
	 * Property overloading. Allows accessing model data as attributes.
	 *
	 * <code>
	 *   $user = User::find(25);
	 *   echo $user->first_name;
	 * </code>
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function __get($attribute) {
		if (array_key_exists($attribute, $this->_data)) {
			return $this->_data[$attribute];
		}

		throw new Rox_Exception("unknown attribute {$attribute}");
	}

	/**
	 * Property overloading. Allows accessing model data as attributes.
	 *
	 * <code>
	 *   $user = new User;
	 *   $user->first_name = "John";
	 *   $user->last_name = "Doe";
	 *   $user->save();
	 * </code>
	 *
	 * @param string $var
	 */
	public function __set($attribute, $value) {
		if (strpos($attribute, '_') === 0) {
			$this->{$attribute} = $value;
			return;
		}

		$this->setData($attribute, $value);
	}

	public function __isset($attribute) {
		return array_key_exists($attribute, $this->_data);
	}

	/**
	 * Runs the validation callbacks
	 *
	 * @return boolean
	 */
	public function valid() {
		if ($this->_errors === null) {
			$this->_errors = new Rox_ActiveModel_ErrorCollection;
		} else {
			$this->_errors->clear();
		}

		$this->_validate();

		if ($this->_newRecord) {
			$this->_validateOnCreate();
		} else {
			$this->_validateOnUpdate();
		}

		return count($this->_errors) == 0;
	}

	/**
	 * Returns the validation errors
	 *
	 * @return array
	 */
	public function getValidationErrors() {
		if ($this->_errors === null) {
			return array();
		}

		return $this->_errors->toArray();
	}

	/**
	 * Creates an object and save it to the database.
	 *
	 * @param mixed $data
	 * @return object
	 */
	public function create($data) {
		$className = get_class($this);
		$object = new $className($data);
		$object->save();
		return $object;
	}

	/**
	 * Updates the passed attributes and saves the record.
	 *
	 * @param array $attributes
	 * @return boolean
	 */
	public function updateAttributes($attributes) {
		$this->setData($attributes);
		return $this->save();
	}

	// ---------------------------------------------
	//  Validation methods
	// ---------------------------------------------

	/**
	 * Validates that specified attributes are not empty
	 *
	 * @param string|array $attributeNames
	 * @param string $message
	 * @return void
	 */
	protected function _validatesPresenceOf($attributeNames, $message = "cannot be left blank") {
		foreach ((array)$attributeNames as $attributeName) {
			if (empty($this->_data[$attributeName]) || trim($this->_data[$attributeName]) == '') {
				$this->_errors->add($attributeName, $message);
			}
		}
	}

	/**
	 * Validates the acceptance of agreements checkboxes
	 *
	 * @param string|array $attributeNames
	 * @param string $message
	 * @return void
	 */
	protected function _validatesAcceptanceOf($attributeNames, $message = 'must be accepted') {
		foreach ((array)$attributeNames as $attributeName) {
			if ($this->getData($attributeName) != '1') {
				$this->_errors->add($attributeName, $message);
			}
		}
	}

	// ---------------------------------------------
	//  Validation callbacks
	// ---------------------------------------------

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	protected function _validate() {
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	protected function _validateOnCreate() {
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	protected function _validateOnUpdate() {
	}

	// ---------------------------------------------
	//  Callbacks
	// ---------------------------------------------

	/**
	 * Before save callback
	 *
	 * @return void
	 */
	protected function _beforeSave() {
	}

	/**
	 * After save callback
	 *
	 * @param boolean $created
	 * @return void
	 */
	protected function _afterSave($created) {
	}

	/**
	 * Before delete callback
	 *
	 * @return void
	 */
	protected function _beforeDelete() {
	}

	/**
	 * After delete callback
	 *
	 * @return void
	 */
	protected function _afterDelete() {
	}
}

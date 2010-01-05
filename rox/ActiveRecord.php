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
 * Data types
 */
define('DATATYPE_STRING', 'string');
define('DATATYPE_INTEGER', 'integer');
define('DATATYPE_DATE', 'date');
define('DATATYPE_DATETIME', 'datetime');
define('DATATYPE_BOOLEAN', 'boolean');
define('DATATYPE_BINARY', 'binary');
define('DATATYPE_FLOAT', 'float');

/**
 * Rox_ActiveRecord class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
abstract class Rox_ActiveRecord {

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_table;

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
	 * Attribute map
	 *
	 * @var array
	 */
	protected $_attributeMap;

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
	 * @var Rox_ActiveRecord_ErrorCollection
	 */
	protected $_errors;

	/**
	 * Constructor
	 *
	 * @param array $attributes
	 * @return void
	 */
	public function __construct(array $attributes = null) {
		if ($this->_table === null) {
			$this->_table = Rox_Inflector::tableize(get_class($this));
		}

		if ($attributes !== null) {
			$this->setData($attributes);
		}
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
	 * Method overloading
	 *
	 * @param string $method method name
	 * @param array $args arguments
	 * @return mixed
	 * @throws Exception
	 * @link http://us.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 */
	public function __call($method, $args) {
		switch (substr($method, 0, 3)) {
			case 'get':
				$key = Rox_Inflector::underscore(substr($method, 3));
				return $this->getData($key, isset($args[0]) ? $args[0] : null);

			case 'set':
				$key = Rox_Inflector::underscore(substr($method, 3));
				return $this->setData($key, isset($args[0]) ? $args[0] : null);
		}

		if (strpos($method, 'findBy') === 0) {
			$key = Rox_Inflector::underscore(substr($method, 6));
			return $this->find(array($key => $args[0]));
		}

		throw new Exception('Invalid method '.get_class($this).'::'.$method.'()');
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
	public function __get($var) {
		$var = Rox_Inflector::underscore($var);
		return $this->getData($var);
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
	 * @return mixed
	 */
	public function __set($var, $value) {
		$var = Rox_Inflector::underscore($var);
		$this->setData($var, $value);
	}

	/**
	 * Runs the validation callbacks
	 *
	 * @return boolean
	 */
	public function valid() {
		if ($this->_errors === null) {
			$this->_errors = new Rox_ActiveRecord_ErrorCollection($this->_data);
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
	 * Saves the model
	 *
	 * @return boolean
	 */
	public function save() {
		if (!$this->valid()) {
			return false;
		}

		$this->_beforeSave();

		$data = $this->getData();
		if (empty($data)) {
			return false;
		}

		$attributeMap = $this->_attributeMap();
		foreach ($data as $k => $v) {
			if (!array_key_exists($k, $attributeMap) || !in_array($k, $this->_modifiedAttributes)) {
				unset($data[$k]);
			}
		}

		unset($data[$this->_primaryKey]);

		$dataSource = Rox_ConnectionManager::getDataSource($this->_dataSourceName);

		if ($this->_newRecord) {
			if ($this->hasAttribute('created_at') && !isset($data['created_at'])) {
				$data['created_at'] = date('Y-m-d H:i:s');
			}

			foreach ($data as $f => $v) {
				$data[$f] = $this->smartQuote($f, $v);
			}

			$attributes = '`' . implode('`, `', array_keys($data)) . '`';
			$values = implode(', ', array_values($data));
			$sql = sprintf(
				"INSERT INTO `%s` (%s) VALUES (%s)",
				$this->_table,
				$attributes,
				$values
			);

			$dataSource->execute($sql);
			if ($dataSource->affectedRows() == 1) {
				$this->setId($dataSource->lastInsertedID());
				$this->_resetModifiedAttributesFlags();
				$this->_newRecord = false;
				$this->_afterSave(true);
				return true;
			}
		} else {
			if ($this->hasAttribute('updated_at') && !isset($data['updated_at'])) {
				$data['updated_at'] = date('Y-m-d H:i:s');
			}

			$updateData = array();
			foreach ($data as $f => $v) {
				$updateData[] = '`' . $f . '` = ' . $this->smartQuote($f, $v);
			}

			$sql = sprintf(
				"UPDATE `%s` SET %s WHERE `%s` = %s",
				$this->_table,
				implode(', ', $updateData),
				$this->_primaryKey,
				$this->smartQuote($this->_primaryKey, $this->getId())
			);

			if ($dataSource->execute($sql) !== false) {
				$this->_resetModifiedAttributesFlags();
				$this->_afterSave(false);
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns true if a record exists
	 *
	 * @param mixed $idOrConditions
	 * @return boolean
	 */
	public function exists($idOrConditions = array()) {
		if (!is_array($idOrConditions)) {
			$idOrConditions = array($this->_primaryKey => $idOrConditions);
		}

		$sql = sprintf("SELECT COUNT(*) AS `count` FROM `%s`", $this->_table);
		$sql.= $this->_buildConditionsSQL($idOrConditions);
		$sql.= ' LIMIT 1';

		$result = Rox_ConnectionManager::getDataSource($this->_dataSourceName)->query($sql);
		return $result[0]['count'] == 1;
	}

	/**
	 * Rox_ActiveRecord_Abstract::findCount()
	 *
	 * @param array|string $conditions
	 * @return integer
	 */
	public function findCount($conditions = array()) {
		$sql = sprintf('SELECT COUNT(*) AS `count` FROM `%s`', $this->_table);
		$sql.= $this->_buildConditionsSQL($conditions);

		$dataSource = Rox_ConnectionManager::getDataSource($this->_dataSourceName);
		$result = $dataSource->query($sql);

		return (integer)$result[0]['count'];
	}

	/**
	 * Rox_ActiveRecord::findAll()
	 *
	 * @param array|string $conditions
	 * @param array $options  
	 * @return array
	 */
	public function findAll($conditions = array(), $options = array()) {
		$options = array_merge(array(
			'attributes' => null,
			'order'  => null,
			'limit'  => null
		), $options);

		if ($options['attributes'] === null) {
			$options['attributes'] = '*';
		} else if (is_array($options['attributes'])) {
			$options['attributes'] = '`' . implode('`, `', $options['attributes']) . '`';
		}

		$sql = sprintf('SELECT %s FROM `%s`', $options['attributes'], $this->_table);
		$sql.= $this->_buildConditionsSQL($conditions);

		if (!empty($options['order'])) {
			$sql .= ' ORDER BY ' . $options['order'];
		}

		if (!empty($options['limit'])) {
			$sql .= ' LIMIT ' . $options['limit'];
		}

		$result = $this->findBySql($sql);
		return $result;
	}

	/**
	 * Rox_ActiveRecord::paginate()
	 * 
	 * @param array $options
	 * @return Rox_ActiveRecord_PaginationResult
	 */
	public function paginate($options = array()) {
		$defaultOptions = array(
			'per_page'   => 10,
			'page'       => 1,
			'conditions' => array(),
			'order'      => null,
			'attributes'     => null
		);

		$options = array_merge($defaultOptions, $options);

		$pages = 1;
		$currentPage = 1;
		$items = array();

		$total = $this->findCount($options['conditions']);
		if ($total > 0) {
			$pages = (integer)ceil($total / $options['per_page']);
			$currentPage = min(max(intval($options['page']), 1), $pages);
			$limit = sprintf('%d, %d', ($currentPage - 1) * $options['per_page'], $options['per_page']);
			$items = $this->findAll($options['conditions'], array(
				'attributes' => $options['attributes'],
				'order'  => $options['order'],
				'limit'  => $limit
			));
		}

		$nextPage = min($pages, $currentPage + 1);
		$previousPage = max(1, $currentPage - 1);

		$result = new Rox_ActiveRecord_PaginationResult($items, $pages, $currentPage,
			$nextPage, $previousPage, $total);
		return $result;
	}

	/**
	 * Rox_ActiveRecord::find()
	 *
	 * @param mixed $attributes
	 * @param array $options
	 * @return object
	 * @throws Rox_ActiveRecord_RecordNotFound
	 */
	public function find($conditions = array(), $options = array()) {
		$checkResult = false;

		$options = array_merge(array(
			'attributes' => null,
			'order' => null
		), $options, array('limit' => 1));

		if (!is_array($conditions)) {
			$conditions = array($this->_primaryKey => $conditions);
			$checkResult = true;
		}

		$result = $this->findAll($conditions, $options);
		$result = reset($result);
		if ($checkResult && !$result) {
			throw new Rox_ActiveRecord_RecordNotFound;
		}

		return $result;
	}

	/**
	 * Rox_ActiveRecord_Abstract::findLast()
	 * 
	 * @param array|string $conditions
	 * @param array|string $attributes
	 * @return object
	 */
	public function findLast($conditions = null, $attributes = null) {
		$options = array(
			'attributes' => $attributes,
			'order' => '`'.$this->_primaryKey.'` DESC',
			'limit' => 1
		);

		$results = $this->findAll($conditions, $options);
		return reset($results);
	}

	/**
	 * Finds records by SQL
	 * 
	 * @param string $sql
	 * @return array
	 */
	public function findBySql($sql) {
		$dataSource = Rox_ConnectionManager::getDataSource($this->_dataSourceName);
		$rows = $dataSource->query($sql);

		$className = get_class($this);

		$results = array();
		foreach ($rows as $row) {
			$object = new $className();
			$object->_data = $row;
			$object->_newRecord = false;
			$results[] = $object;
		}

		return $results;
	}

	/**
	 * Deletes a record
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id = null) {
		if (empty($id)) {
			$id = $this->getId();
		}

		if (empty($id)) {
			return false;
		}

		$this->_beforeDelete();

		$sql = sprintf(
			"DELETE FROM `%s` WHERE `%s` = %s",
			$this->_table,
			$this->_primaryKey,
			$this->smartQuote($this->_primaryKey, $id)
		);

		$dataSource = Rox_ConnectionManager::getDataSource($this->_dataSourceName);
		$dataSource->execute($sql);

		$deleted = $dataSource->affectedRows() > 0;
		if ($deleted) {
			$this->_afterDelete();
		}

		return $deleted;
	}

	/**
	 * Finds and deletes all records that match $conditions
	 *
	 * @param array|string $conditions 
	 * @return void
	 */
	public function deleteAll($conditions = array()) {
		$records = $this->findAll($conditions);
		foreach ($records as $record) {
			$record->delete();
		}
	}

	/**
	 * Quotes and escapes values to be used in SQL queries  
	 *
	 * @param string $attribute
	 * @param mixed $value
	 * @return mixed
	 */
	public function smartQuote($attribute, $value) {
		if (null === $value) {
			return 'NULL';
		}

		$type = DATATYPE_STRING;

		$attributeMap = $this->_attributeMap();
		if (isset($attributeMap[$attribute])) {
			$type = $attributeMap[$attribute];
		}

		switch ($type) {
			case DATATYPE_INTEGER:
				return (integer)$value;

			case DATATYPE_BOOLEAN:
				return $value ? '1' : '0';

			case DATATYPE_STRING:
			case DATATYPE_DATE:
			case DATATYPE_DATETIME:
			case DATATYPE_BINARY:
				return "'" . Rox_ConnectionManager::getDataSource($this->_dataSourceName)->escape($value) . "'";

			case DATATYPE_FLOAT:
				return (float)$value;
		}
	}

	/**
	 * Rox_ActiveRecord_Abstract::_attributeMap()
	 * 
	 * @return array
	 */
	protected function _attributeMap() {
		if (null === $this->_attributeMap) {
			$db = Rox_ConnectionManager::getDataSource($this->_dataSourceName);
			$this->_attributeMap = $db->generateAttributeMapFromTable($this->_table);
		}

		return $this->_attributeMap;	
	}

	/**
	 * Checks if a attribute exists
	 *
	 * @param string $attribute
	 * @return boolean
	 */
	public function hasAttribute($attribute) {
		return array_key_exists($attribute, $this->_attributeMap());
	}

	/**
	 * Rox_ActiveRecord_Abstract::_buildConditionsSQL()
	 * 
	 * @param mixed $conditions
	 * @return string
	 */
	protected function _buildConditionsSQL($conditions) {
		if (empty($conditions)) {
			return null;
		}

		if (is_string($conditions)) {
			$sql = ' WHERE ' . $conditions;
			return $sql;
		}

		$normalizedConditions = array();
		foreach ($conditions as $f => $v) {
			if (is_int($f)) {
				$normalizedConditions[] = ' ' . $v;
			} else {
				$normalizedConditions[] = ' `' . $f . '` = ' . $this->smartQuote($f, $v);
			}
		}

		$sql = ' WHERE ' . implode(' AND ', $normalizedConditions);
		return $sql;
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
	public function _validatesPresenceOf($attributeNames, $message = "can't be blank") {
		if (!is_array($attributeNames)) {
			$attributeNames = array($attributeNames);
		}

		foreach ($attributeNames as $attributeName) {
			$this->_errors->addOnEmpty($attributeName, $message);
		}
	}

	/**
	 * Validates the acceptance of agreements checkboxes
	 *
	 * @param string|array $attributeNames 
	 * @param string $message 
	 * @return void
	 */
	public function _validatesAcceptanceOf($attributeNames, $message = 'must be accepted') {
		if (!is_array($attributeNames)) {
			$attributeNames = array($attributeNames);
		}

		foreach ($attributeNames as $attributeName) {
			if ($this->getData($attributeName) != '1') {
				$this->_errors->add($attributeName, $message);
			}
		}
	}

	/**
	 * Validates that specified attributes are unique in the model database table.
	 *
	 * <code>
	 * class User extends Rox_ActiveRecord {
	 *     protected function _validate() {
	 *         $this->_validatesUniquenessOf('username');
	 *     }
	 * }
	 * </code>
	 *
	 * Config options:
	 * - message: Custom error message (default: "has already been taken").
	 * - scope: Columns that define the scope.
	 *
	 * @param array|string $attributes
	 * @param array|string $options
	 * @return void
	 */
	protected function _validatesUniquenessOf($attributes, $options = array()) {
		$defaultOptions = array(
			'message' => 'has already been taken',
			'scope'   => array()
		);

		$options = array_merge($defaultOptions, $options);

		$scopeConditions = array();
		foreach ((array)$options['scope'] as $scopeAttribute) {
			$scopeConditions[$scopeAttribute] = $this->getData($scopeAttribute);
		}

		if (!$this->_newRecord) {
			$scopeConditions[] = sprintf("`%s` != %s", $this->_primaryKey,
				$this->smartQuote($this->_primaryKey, $this->getId()));
		}

		foreach ((array)$attributes as $attribute) {
			$conditions = array($attribute => $this->getData($attribute));
			$conditions += $scopeConditions;

			if ($this->exists($conditions)) {
				$this->_errors->add($attribute, $options['message']);
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

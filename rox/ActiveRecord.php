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
 * Rox_ActiveRecord class
 *
 * @package Rox
 */
abstract class Rox_ActiveRecord extends Rox_ActiveModel {

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_table;

	/**
	 * Attribute map
	 *
	 * @var array
	 */
	protected $_attributeMap;

	/**
	 * List of one-to-many associations
	 *
	 * @var array
	 */
	protected $_hasMany = array();

	/**
	 * List of one-to-one associations
	 *
	 * @var array
	 */
	protected $_hasOne = array();

	/**
	 * List of one-to-one associations
	 *
	 * @var array
	 */
	protected $_belongsTo = array();

	/**
	 * Timezone of magic timestamp attributes
	 *
	 * @var string
	 */
	protected $_timestampsTimezone = 'local';

	/**
	 * Constructor
	 *
	 * @param array $attributes
	 * @return void
	 */
	public function __construct(array $attributes = null) {
		parent::__construct($attributes);

		if ($this->_table === null) {
			$this->_table = Rox_Inflector::tableize(get_class($this));
		}
	}

	/**
	 * Method overloading
	 *
	 * @param string $method method name
	 * @param array $args arguments
	 * @return mixed
	 * @throws Rox_Exception
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
			return $this->findFirst(array('conditions' => array($key => $args[0])));
		}

		$assoc = $this->_association('belongs_to', $method);
		if ($assoc) {
			$attributeName = '_' . $method;
			if (!isset($this->{$attributeName})) {
				$this->{$attributeName} = self::model($assoc['class'])->findFirst(array(
					'conditions' => array('id' => $this->{$assoc['key']})
				));
			}

			return $this->{$attributeName};
		}

		$assoc = $this->_association('has_one', $method);
		if ($assoc) {
			$attributeName = '_' . $method;
			if (!isset($this->{$attributeName})) {
				$this->{$attributeName} = self::model($assoc['class'])->findFirst(array(
					'conditions' => array($assoc['key'] => $this->getId())
				));
			}

			return $this->{$attributeName};
		}

		throw new Rox_Exception('Invalid method '.get_class($this).'::'.$method.'()');
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

		$assoc = $this->_association('has_many', $attribute);
		if ($assoc) {
			$class = $assoc['class'];
			$scope = array($assoc['key'] => $this->getId());
			return $this->{$attribute} = new Rox_ActiveRecord_Association_Collection($class, $scope);
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
	 * undocumented function
	 *
	 * @return array
	 */
	protected function _association($type, $name) {
		static $associations;
		if ($associations === null) {
			$associations = $this->_normalizeAssociations();
		}

		return isset($associations[$type][$name]) ? $associations[$type][$name] : false;
	}

	/**
	 * undocumented function
	 *
	 * @return array
	 */
	private function _normalizeAssociations() {
		$associations = array('belongs_to' => array(), 'has_many' => array(), 'has_one' => array());
		$rels = array('belongs_to' => '_belongsTo', 'has_many' => '_hasMany', 'has_one' => '_hasOne');
		foreach ($rels as $type => $property) {
			foreach ($this->{$property} as $name => $options) {
				if (is_int($name) && is_string($options)) {
					$name = $options;
					$options = array();
				}

				$keyClass = ($type == 'belongs_to') ? $name : get_class($this);
				$defaults = array(
					'class' => Rox_Inflector::classify($name),
					'key' => Rox_Inflector::underscore($keyClass) . '_id'
				);

				$options += $defaults;
				$associations[$type][$name] = $options;
			}
		}

		return $associations;
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
		$this->_updateMagicTimestamps();

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

		$dataSource = $this->datasource();

		if ($this->_newRecord) {
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
	 * Updates the magic timestamp attributes (created_at and updated_at)
	 *
	 * @return void
	 */
	protected function _updateMagicTimestamps() {
		$timestamp = $this->_timestampsTimezone == 'local' ?
			date('Y-m-d H:i:s') : gmdate('Y-m-d H:i:s');

		if ($this->_newRecord && $this->hasAttribute('created_at') && empty($this->created_at)) {
			$this->created_at = $timestamp;
		}

		if ($this->hasAttribute('updated_at') && empty($data->updated_at)) {
			$this->updated_at = $timestamp;
		}
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

		$result = $this->datasource()->query($sql);
		return $result[0]['count'] > 0;
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

		$dataSource = $this->datasource();
		$result = $dataSource->query($sql);

		return (integer)$result[0]['count'];
	}

	/**
	 * Rox_ActiveRecord::findAll()
	 *
	 * @param array $options  
	 * @return array
	 */
	public function findAll($options = array()) {
		$defaults = array(
			'conditions' => array(),
			'attributes' => null,
			'order'      => null,
			'limit'      => null,
			'group'      => null
		);

		$options += $defaults;

		if (empty($options['attributes'])) {
			$options['attributes'] = '*';
		} else if (is_array($options['attributes'])) {
			$options['attributes'] = '`' . implode('`, `', $options['attributes']) . '`';
		}

		$sql = sprintf('SELECT %s FROM `%s`', $options['attributes'], $this->_table);
		$sql.= $this->_buildConditionsSQL($options['conditions']);

		if (!empty($options['group'])) {
			$sql .= ' GROUP BY ' . $options['group'];
		}

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
			'attributes' => null,
			'group'      => null
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
			$items = $this->findAll(array(
				'conditions' => $options['conditions'],
				'attributes' => $options['attributes'],
				'order'      => $options['order'],
				'limit'      => $limit,
				'group'      => $options['group']
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
	 * @param integer|string|array $ids
	 * @param array $options
	 * @return array|Rox_ActiveRecord
	 * @throws Rox_ActiveRecord_RecordNotFound
	 */
	public function find($ids, $options = array()) {
		$checkArray = is_array($ids);

		$options = array_merge_recursive(array('attributes' => null, 'order' => null),
			$options, array('conditions' => array($this->_primaryKey => $ids)));

		$results = $this->findAll($options);
		if ($checkArray) {
			if (count($results) == count($ids)) {
				return $results;
			} else {
				throw new Rox_ActiveRecord_RecordNotFound("Couldn't find all with IDs ({$ids})");
			}
		}

		$result = reset($results);
		if (!$result) {
			throw new Rox_ActiveRecord_RecordNotFound("Couldn't find record with ID = {$ids}");
		}

		return $result;
	}

	/**
	 * Rox_ActiveRecord_Abstract::findFirst()
	 *
	 * @param array $options 
	 * @return Rox_ActiveRecord
	 */
	public function findFirst($options = array()) {
		$options = array_merge($options, array(
			'limit' => 1
		));

		$results = $this->findAll($options);
		return reset($results);
	}

	/**
	 * Rox_ActiveRecord_Abstract::findLast()
	 * 
	 * @param array $options
	 * @return Rox_ActiveRecord
	 */
	public function findLast($options = array()) {
		$options = array_merge($options, array(
			'order' => '`' . $this->_primaryKey . '` DESC',
		));

		return $this->findFirst($options);
	}

	/**
	 * Finds records by SQL
	 * 
	 * @param string $sql
	 * @return array
	 */
	public function findBySql($sql) {
		$rows = $this->datasource()->query($sql);

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
	 * Deletes the record
	 *
	 * @return boolean
	 */
	public function delete() {
		if ($this->_newRecord) {
			throw new Rox_Exception("You can't delete new records");
		}

		$this->_beforeDelete();

		$sql = sprintf(
			"DELETE FROM `%s` WHERE `%s` = %s",
			$this->_table,
			$this->_primaryKey,
			$this->smartQuote($this->_primaryKey, $this->getId())
		);

		$dataSource = $this->datasource();
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
		$records = $this->findAll(array('conditions' => $conditions));
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

		$type = 'string';

		$attributeMap = $this->_attributeMap();
		if (isset($attributeMap[$attribute])) {
			$type = $attributeMap[$attribute];
		}

		switch ($type) {
			case 'integer':
				return (integer)$value;

			case 'boolean':
				return $value ? '1' : '0';

			case 'string':
			case 'date':
			case 'datetime':
			case 'binary':
				return "'" . $this->datasource()->escape($value) . "'";

			case 'float':
				return (float)$value;
		}
	}

	/**
	 * Rox_ActiveRecord_Abstract::_attributeMap()
	 * 
	 * @return array
	 */
	protected function _attributeMap() {
		if ($this->_attributeMap === null) {
			$db = $this->datasource();
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
	 * undocumented function
	 *
	 * @return Rox_DataSource
	 */
	public function datasource() {
		return Rox_ConnectionManager::getDataSource($this->_dataSourceName);
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
		foreach ($conditions as $field => $value) {
			if (is_int($field)) {
				$normalizedConditions[] = '(' . $value . ')';
			} else if (is_array($value)) {
				foreach ($value as &$valueRef) {
					$valueRef = $this->smartQuote($field, $valueRef);
				}
				$normalizedConditions[] = '`' . $field . '` IN(' . implode(', ', $value) . ')';
			} else {
				$normalizedConditions[] = '`' . $field . '` = ' . $this->smartQuote($field, $value);
			}
		}

		$sql = ' WHERE ' . implode(' AND ', $normalizedConditions);
		return $sql;
	}

	// ---------------------------------------------
	//  Validation methods
	// ---------------------------------------------

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
}

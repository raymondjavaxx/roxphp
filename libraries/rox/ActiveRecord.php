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

namespace rox;

use \rox\Inflector;
use \rox\Exception;
use \rox\active_record\AssociationCollection;
use \rox\active_record\RecordNotFoundException;
use \rox\active_record\ConnectionManager;
use \rox\active_record\PaginationResult;

/**
 * ActiveRecord class
 *
 * @package Rox
 */
abstract class ActiveRecord extends \rox\ActiveModel {

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected static $_table;

	/**
	 * Attribute map
	 *
	 * @var array
	 */
	protected static $_attributeMap;

	/**
	 * List of one-to-many associations
	 *
	 * @var array
	 */
	protected static $_hasMany = array();

	/**
	 * List of one-to-one associations
	 *
	 * @var array
	 */
	protected static $_hasOne = array();

	/**
	 * List of one-to-one associations
	 *
	 * @var array
	 */
	protected static $_belongsTo = array();

	/**
	 * Timezone of magic timestamp attributes
	 *
	 * @var string
	 */
	protected static $_timestampsTimezone = 'local';

	/**
	 * Returns the name of db table
	 *
	 * @return string
	 */
	public static function _table() {
		if (static::$_table === null) {
			$table = Inflector::tableize(get_called_class());
			static::$_table = &$table;
		}

		return static::$_table;
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
		$assoc = $this->_association('belongs_to', $method);
		if ($assoc) {
			$attributeName = '_' . $method;
			if (!isset($this->{$attributeName})) {
				$this->{$attributeName} = $assoc['class']::findFirst(array(
					'conditions' => array('id' => $this->{$assoc['key']})
				));
			}

			return $this->{$attributeName};
		}

		$assoc = $this->_association('has_one', $method);
		if ($assoc) {
			$attributeName = '_' . $method;
			if (!isset($this->{$attributeName})) {
				$this->{$attributeName} = $assoc['class']::findFirst(array(
					'conditions' => array($assoc['key'] => $this->getId())
				));
			}

			return $this->{$attributeName};
		}

		throw new Exception('Invalid method ' . get_class($this) . '::' . $method . '()');
	}

	public static function __callStatic($method, $args) {
		if (strpos($method, 'findBy') === 0) {
			$key = Inflector::underscore(substr($method, 6));
			return static::findFirst(array('conditions' => array($key => $args[0])));
		}

		throw new Exception('Invalid static function ' . get_called_class() . '::' . $method . '()');
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
			$through = (isset($assoc['through_model'])) ? $assoc['through_model'] : null;
			return $this->{$attribute} = new AssociationCollection($class, $scope, $through);
		}

		throw new Exception("unknown attribute {$attribute}");
	}

	/**
	 * Property overloading. Allows accessing model data as attributes.
	 *
	 *    $user = new User;
	 *    $user->first_name = "John";
	 *    $user->last_name = "Doe";
	 *    $user->save();
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
			$associations = static::_normalizeAssociations();
		}
		return isset($associations[$type][$name]) ? $associations[$type][$name] : false;
	}

	/**
	 * undocumented function
	 *
	 * @return array
	 */
	protected static function _normalizeAssociations() {
		$associations = array('belongs_to' => array(), 'has_many' => array(), 'has_one' => array());
		$rels = array('belongs_to' => '_belongsTo', 'has_many' => '_hasMany', 'has_one' => '_hasOne');
		foreach ($rels as $type => $property) {
			foreach (static::$$property as $name => $options) {
				if (is_int($name) && is_string($options)) {
					$name = $options;
					$options = array();
				}

				$keyClass = ($type == 'belongs_to') ? $name : get_called_class();

				if ($type == 'has_many' && isset($options['through'])) {
					$options['through_model'] = Inflector::classify($options['through']);
				}

				$defaults = array(
					'class' => Inflector::classify($name),
					'key' => Inflector::underscore($keyClass) . '_id'
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
	 * @param $options array
	 *         - validate: bool, whether or not to validate before saving (defaults to true)
	 * @return boolean
	 */
	public function save($options = array()) {
		$defaults = array('validate' => true);
		$options += $defaults;

		if ($options['validate'] && !$this->valid()) {
			return false;
		}

		$this->_beforeSave();
		$this->_updateMagicTimestamps();

		$data = $this->getData();
		$attributeMap = $this->_attributeMap();

		$modifiedAttributes = array_intersect($this->_modifiedAttributes, array_keys($attributeMap));
		$data = array_intersect_key($data, array_flip($modifiedAttributes));
		unset($data[static::$_primaryKey]);

		$dataSource = static::datasource();

		if ($this->_newRecord) {
			foreach ($data as $f => $v) {
				$data[$f] = $this->smartQuote($f, $v);
			}

			$attributes = '`' . implode('`, `', array_keys($data)) . '`';
			$values = implode(', ', array_values($data));
			$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s)", static::_table(), $attributes, $values);

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
				static::_table(),
				implode(', ', $updateData),
				static::$_primaryKey,
				$this->smartQuote(static::$_primaryKey, $this->getId())
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
		$timestamp = static::$_timestampsTimezone == 'local'
			? date('Y-m-d H:i:s') : gmdate('Y-m-d H:i:s');

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
	public static function exists($idOrConditions = array()) {
		if (!is_array($idOrConditions)) {
			$idOrConditions = array(static::$_primaryKey => $idOrConditions);
		}

		$sql = sprintf("SELECT COUNT(*) AS `count` FROM `%s`", static::_table());
		$sql.= static::_buildConditionsSQL($idOrConditions);
		$sql.= ' LIMIT 1';

		$result = static::datasource()->query($sql);
		return $result[0]['count'] > 0;
	}

	/**
	 * ActiveRecord::findCount()
	 *
	 * @param array|string $conditions
	 * @return integer
	 */
	public static function findCount($conditions = array()) {
		$sql = sprintf('SELECT COUNT(*) AS `count` FROM `%s`', static::_table());
		$sql.= static::_buildConditionsSQL($conditions);

		$result = static::datasource()->query($sql);

		return (integer)$result[0]['count'];
	}

	/**
	 * ActiveRecord::findAll()
	 *
	 * @param array $options  
	 * @return array
	 */
	public static function findAll($options = array()) {
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

		$sql = sprintf('SELECT %s FROM `%s`', $options['attributes'], static::_table());
		$sql.= static::_buildConditionsSQL($options['conditions']);

		if (!empty($options['group'])) {
			$sql .= ' GROUP BY ' . $options['group'];
		}

		if (!empty($options['order'])) {
			$sql .= ' ORDER BY ' . $options['order'];
		}

		if (!empty($options['limit'])) {
			$sql .= ' LIMIT ' . $options['limit'];
		}

		$result = static::findBySql($sql);
		return $result;
	}

	/**
	 * ActiveRecord::paginate()
	 * 
	 * @param array $options
	 * @return ActiveRecord_PaginationResult
	 */
	public static function paginate($options = array()) {
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

		$total = static::findCount($options['conditions']);
		if ($total > 0) {
			$pages = (integer)ceil($total / $options['per_page']);
			$currentPage = min(max(intval($options['page']), 1), $pages);
			$limit = sprintf('%d, %d', ($currentPage - 1) * $options['per_page'], $options['per_page']);
			$items = static::findAll(array(
				'conditions' => $options['conditions'],
				'attributes' => $options['attributes'],
				'order'      => $options['order'],
				'limit'      => $limit,
				'group'      => $options['group']
			));
		}

		$nextPage = min($pages, $currentPage + 1);
		$previousPage = max(1, $currentPage - 1);

		$result = new PaginationResult($items, $pages, $currentPage,
			$nextPage, $previousPage, $total);
		return $result;
	}

	/**
	 * ActiveRecord::find()
	 *
	 * @param integer|string|array $ids
	 * @param array $options
	 * @return array|ActiveRecord
	 * @throws RecordNotFoundException
	 */
	public static function find($ids, $options = array()) {
		$checkArray = is_array($ids);

		$options = array_merge_recursive(array('attributes' => null, 'order' => null),
			$options, array('conditions' => array(static::$_primaryKey => $ids)));

		$results = static::findAll($options);
		if ($checkArray) {
			if (count($results) == count($ids)) {
				return $results;
			} else {
				throw new RecordNotFoundException("Couldn't find all with IDs ({$ids})");
			}
		}

		$result = reset($results);
		if (!$result) {
			throw new RecordNotFoundException("Couldn't find record with ID = {$ids}");
		}

		return $result;
	}

	/**
	 * ActiveRecord::findFirst()
	 *
	 * @param array $options 
	 * @return ActiveRecord
	 */
	public static function findFirst($options = array()) {
		$options = array_merge($options, array(
			'limit' => 1
		));

		$results = static::findAll($options);
		return reset($results);
	}

	/**
	 * ActiveRecord::findLast()
	 * 
	 * @param array $options
	 * @return ActiveRecord
	 */
	public static function findLast($options = array()) {
		$options = array_merge($options, array(
			'order' => '`' . static::$_primaryKey . '` DESC',
		));

		return static::findFirst($options);
	}

	/**
	 * Finds records by SQL
	 * 
	 * @param string $sql
	 * @return array
	 */
	public static function findBySql($sql) {
		$rows = static::datasource()->query($sql);

		$className = get_called_class();

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
			throw new Exception("You can't delete new records");
		}

		$this->_beforeDelete();

		$sql = sprintf(
			"DELETE FROM `%s` WHERE `%s` = %s",
			static::_table(),
			static::$_primaryKey,
			$this->smartQuote(static::$_primaryKey, $this->getId())
		);

		$dataSource = static::datasource();
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
	public static function deleteAll($conditions = array()) {
		$records = static::findAll(array('conditions' => $conditions));
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
	public static function smartQuote($attribute, $value) {
		if (null === $value) {
			return 'NULL';
		}

		$type = 'string';

		$attributeMap = static::_attributeMap();
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
				return "'" . static::datasource()->escape($value) . "'";

			case 'float':
				return (float)$value;
		}
	}

	/**
	 * ActiveRecord::_attributeMap()
	 * 
	 * @return array
	 */
	protected static function _attributeMap() {
		if (static::$_attributeMap === null) {
			$db = static::datasource();
			$attributeMap = $db->generateAttributeMapFromTable(static::_table());
			static::$_attributeMap = &$attributeMap;
		}

		return static::$_attributeMap;	
	}

	/**
	 * Checks if a attribute exists
	 *
	 * @param string $attribute
	 * @return boolean
	 */
	public function hasAttribute($attribute) {
		return array_key_exists($attribute, static::_attributeMap());
	}

	/**
	 * undocumented function
	 *
	 * @return \rox\DataSource
	 */
	public static function datasource() {
		return ConnectionManager::getDataSource(static::$_dataSourceName);
	}

	/**
	 * ActiveRecord::_buildConditionsSQL()
	 * 
	 * @param mixed $conditions
	 * @return string
	 */
	protected static function _buildConditionsSQL($conditions) {
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
					$valueRef = static::smartQuote($field, $valueRef);
				}
				$normalizedConditions[] = '`' . $field . '` IN(' . implode(', ', $value) . ')';
			} else {
				$normalizedConditions[] = '`' . $field . '` = ' . static::smartQuote($field, $value);
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
	 * class User extends ActiveRecord {
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
			$scopeConditions[] = sprintf("`%s` != %s", static::$_primaryKey,
				$this->smartQuote(static::$_primaryKey, $this->getId()));
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

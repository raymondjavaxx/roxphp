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
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

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
	 * The name of DataSource used by this Rox_ActiveRecord_Abstract
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
	 * Field map
	 *
	 * @var array
	 */
	protected $_fieldMap;

	/**
	 * List of fields that are protected from massive asignation
	 *
	 * @var array
	 */
	protected $_protectedFields = array('id');

	/**
	 * Array of modified fields
	 *
	 * @var array
	 */
	protected $_modifiedFields = array();

	/**
	 * Used to check if record is new
	 *
	 * @var boolean
	 */
	protected $_newRecord = true;

	/**
	 * Constructor
	 *
	 * @param array $fields
	 * @return void
	 */
	public function __construct(array $fields = null) {
		if (null === $this->_table) {
			$this->_table = Rox_Inflector::tableize(get_class($this));
		}

		if (null !== $fields) {
			$this->setData($fields);
		}
	}

	/**
	 * ID setter
	 *
	 * @param mixed $id 
	 */
	public function setId($id) {
		$this->setData($this->_primaryKey, $id);
	}

	/**
	 * ID getter
	 *
	 * @return mixed $id 
	 */
	public function getId() {
		return $this->getData($this->_primaryKey);
	}

	/**
	 * Flags a given field as "modified"
	 *
	 * @param string $field
	 * @return void
	 */
	protected function _flagFieldAsModified($field) {
		if (!in_array($field, $this->_modifiedFields)) {
			$this->_modifiedFields[] = $field;
		}
	}

	/**
	 * Resets the modified fields list
	 *
	 * @return void
	 */
	protected function _resetModifiedFieldsFlags() {
		$this->_modifiedFields = array();
	}

	/**
	 * Set data
	 *
	 * @param string|array $field
	 * @param mixed $value 
	 */
	public function setData($field, $value = null) {
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if (in_array($k, $this->_protectedFields)) {
					unset($field[$k]);
				}
			}

			$this->_data = array_merge($this->_data, $field);
			$fieldNames = array_keys($field);
			array_walk($fieldNames, array($this, '_flagFieldAsModified'));
		} else {
			$this->_data[$field] = $value;
			$this->_flagFieldAsModified($field);
		}
	}

	/**
	 * Returns the value of a given field.
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function getData($field = null) {
		if (null == $field) {
			return $this->_data;
		}

		if (array_key_exists($field, $this->_data)) {
			return $this->_data[$field];
		}

		return null;
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
	 * Rox_ActiveRecord_Abstract::_fieldMap()
	 * 
	 * @return array
	 */
	protected function _fieldMap() {
		if (null === $this->_fieldMap) {
			$db = Rox_ConnectionManager::getDataSource($this->_dataSourceName);
			$this->_fieldMap = $db->generateFieldMapFromTable($this->_table);
		}

		return $this->_fieldMap;
	}

	/**
	 * Rox_ActiveRecord_Abstract::save()
	 *
	 * @return boolean
	 */
	public function save() {
		$this->_beforeSave();

		$data = $this->getData();
		if (empty($data)) {
			return false;
		}

		$fieldMap = $this->_fieldMap();
		foreach ($data as $k => $v) {
			if (!array_key_exists($k, $fieldMap) || !in_array($k, $this->_modifiedFields)) {
				unset($data[$k]);
			}
		}

		unset($data[$this->_primaryKey]);

		$dataSource = Rox_ConnectionManager::getDataSource($this->_dataSourceName);

		if ($this->_newRecord) {
			if ($this->hasField('created_at') && !isset($data['created_at'])) {
				$data['created_at'] = date('Y-m-d H:i:s');
			}

			foreach ($data as $f => $v) {
				$data[$f] = $this->smartQuote($f, $v);
			}

			$fields = '`' . implode('`, `', array_keys($data)) . '`';
			$values = implode(', ', array_values($data));
			$sql = sprintf(
				"INSERT INTO `%s` (%s) VALUES (%s)",
				$this->_table,
				$fields,
				$values
			);

			$dataSource->execute($sql);
			if ($dataSource->affectedRows() == 1) {
				$this->setId($dataSource->lastInsertedID());
				$this->_resetModifiedFieldsFlags();
				$this->_newRecord = false;
				$this->_afterSave(true);
				return true;
			}
		} else {
			if ($this->hasField('updated_at') && !isset($data['updated_at'])) {
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
				$this->_resetModifiedFieldsFlags();
				$this->_afterSave(false);
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns true if a record exists
	 *
	 * @param mixed $id
	 * @return boolean
	 */
	public function exists($id) {
		$sql = sprintf(
			"SELECT COUNT(*) AS `count` FROM `%s` WHERE `%s` = %s LIMIT 1",
			$this->_table,
			$this->_primaryKey,
			$this->smartQuote($this->_primaryKey, $id)
		);

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
	 * Rox_ActiveRecord_Abstract::findAll()
	 *
	 * @param mixed $fields
	 * @param mixed $conditions
	 * @param string $order
	 * @param mixed $limit   
	 * @return array
	 */
	public function findAll($fields = null, $conditions = array(), $order = null, $limit = null) {
		if (empty($fields)) {
			$fields = '*';
		} else if (is_array($fields)) {
			$fields = '`' . implode('`, `', $fields) . '`';
		}

		$sql = sprintf('SELECT %s FROM `%s`', $fields, $this->_table);
		$sql.= $this->_buildConditionsSQL($conditions);

		if (!empty($order)) {
			$sql .= ' ORDER BY ' . $order;
		}

		if (!empty($limit)) {
			$sql .= ' LIMIT ' . $limit;
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
			'fields'     => null
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
			$items = $this->findAll($options['fields'], $options['conditions'], $options['order'], $limit);
		}

		$nextPage = min($pages, $currentPage + 1);
		$previousPage = max(1, $currentPage - 1);

		$result = new Rox_ActiveRecord_PaginationResult($items, $pages, $currentPage,
			$nextPage, $previousPage);
		return $result;
	}

	/**
	 * Rox_ActiveRecord_Abstract::find()
	 *
	 * @param mixed $fields
	 * @param mixed $conditions
	 * @param string $order
	 * @return object
	 */
	public function find($conditions = array(), $fields = null, $order = null) {
		if (is_numeric($conditions)) {
			$conditions = array('id' => $conditions);
		}

		$results = $this->findAll($fields, $conditions, $order, '1');
		return reset($results);
	}

	/**
	 * Rox_ActiveRecord_Abstract::findLast()
	 * 
	 * @param array|string $conditions
	 * @param array|string $fields
	 * @return object
	 */
	public function findLast($conditions = null, $fields = null) {
		$order = $this->_primaryKey . ' DESC';
		$results = $this->findAll($fields, $conditions, $order, '1');
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
	 * Quotes and escapes values to be used in SQL queries  
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return mixed
	 */
	public function smartQuote($field, $value) {
		if (null === $value) {
			return 'NULL';
		}

		$type = DATATYPE_STRING;

		$fieldMap = $this->_fieldMap();
		if (isset($fieldMap[$field])) {
			$type = $fieldMap[$field];
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
		}
	}

	/**
	 * Checks if a field exists
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function hasField($field) {
		return array_key_exists($field, $this->_fieldMap());
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
	//  Callbacks
	// ---------------------------------------------

	/**
	 * Rox_ActiveRecord_Abstract::_beforeSave()
	 *
	 * @return void
	 */
	protected function _beforeSave() {
	}

	/**
	 * After save callback
	 *
	 * @param boolean $created
	 */
	protected function _afterSave($created) {
	}

	/**
	 * After delete callback
	 */
	protected function _afterDelete() {
	}
}

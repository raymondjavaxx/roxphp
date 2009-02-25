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
 * Model class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Model {

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
	 * @see ConnectionManager::getDataSource()
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
	protected $_fieldMap = array(
		'id' => DATATYPE_INTEGER
	);

	protected $_protectedFields = array('id');

	/**
	 * Array of modified fields
	 *
	 * @var array
	 */
	protected $_modifiedFields = array();

	/**
	 * Used to check if record is record is new
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
	 * Model::_flagFieldAsModified()
	 *
	 * @param mixed $field
	 * @return void
	 */
	protected function _flagFieldAsModified($field) {
		if (!in_array($field, $this->_modifiedFields) &&
			array_key_exists($field, $this->_fieldMap)) {
			$this->_modifiedFields[] = $field;
		}
	}

	/**
	 * Model::_resetModifiedFieldsFlags()
	 *
	 * @return void
	 */
	protected function _resetModifiedFieldsFlags() {
		$this->_modifiedFields = array();
	}

	/**
	 * Set data
	 *
	 * @param string|array $what
	 * @param mixed $value 
	 */
	public function setData($what, $value = null) {
		if (is_array($what)) {
			foreach ($what as $k => $v) {
				if (in_array($k, $this->_protectedFields)) {
					unset($what[$k]);
				}
			}

			$this->_data = array_merge($this->_data, $what);
			$fieldNames = array_keys($what);
			array_walk($fieldNames, array($this, '_flagFieldAsModified'));
		} else {
			$this->_data[$what] = $value;
			$this->_flagFieldAsModified($what);
		}
	}

	/**
	 * Get data
	 *
	 * @param string $what
	 * @return mixed
	 */
	public function getData($what = null) {
		if (empty($what)) {
			return $this->_data;
		}

		if (array_key_exists($what, $this->_data)) {
			return $this->_data[$what];
		}

		return null;
	}

	/**
	 * Creates an object and saves it to the database.
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
	 * Model::save()
	 *
	 * @return boolean
	 */
	public function save() {
		$this->_beforeSave();

		$data = $this->getData();
		if (empty($data)) {
			return false;
		}

		foreach ($data as $k => $v) {
			if (!in_array($k, $this->_modifiedFields)) {
				unset($data[$k]);
			}
		}

		unset($data[$this->_primaryKey]);

		$dataSource = ConnectionManager::getDataSource($this->_dataSourceName);

		if ($this->_newRecord) {
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

		$result = ConnectionManager::getDataSource($this->_dataSourceName)->query($sql);
		return $result[0]['count'] == 1;
	}

	/**
	 * Model::findCount()
	 *
	 * @param mixed $conditions
	 * @return integer
	 */
	public function findCount($conditions = array()) {
		$result = $this->find('COUNT(*) AS `count`', $conditions);
		return (integer)$result['count'];
	}

	/**
	 * Load object by ID
	 *
	 * @param mixed $id
	 * @param array $fields
	 * @return boolean
	 */
	public function read($id, $fields = null) {
		if (empty($fields)) {
			$fields = '*';
		} else if (is_array($fields)) {
			$fields = '`' . implode('`, `', $fields) . '`';
		}

		$sql = sprintf(
			"SELECT %s FROM `%s` WHERE `%s` = %s",
			$fields,
			$this->_table,
			$this->_primaryKey,
			$this->smartQuote($this->_primaryKey, $id)
		);

		$result = ConnectionManager::getDataSource($this->_dataSourceName)->query($sql);
		if (empty($result)) {
			return false;
		}

		$this->setId($id);
		$this->setData($result[0]);
		$this->_newRecord = false;

		return true;
	}

	/**
	 * Model::findAll()
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

		if (!empty($conditions)) {
			$sql .= ' WHERE ';
			if (is_string($conditions)) {
				$sql .= $conditions;
			} else {
				$normalizedConditions = array();
				foreach ($conditions as $f => $v) {
					if (is_int($f)) {
						$normalizedConditions[] = ' ' . $v;
					} else {
						$normalizedConditions[] = ' `' . $f . '` = ' . $this->smartQuote($f, $v);
					}
				}

				$sql .= implode(' AND ', $normalizedConditions);
			}
		}

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
	 * Model::find()
	 *
	 * @param mixed $fields
	 * @param mixed $conditions
	 * @param string $order
	 * @return object
	 */
	public function find($fields = null, $conditions = array(), $order = null) {
		$results = $this->findAll($fields, $conditions, $order, '1');
		return reset($results);
	}

	/**
	 * Model::findLast()
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
		$dataSource = ConnectionManager::getDataSource($this->_dataSourceName);
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

		$dataSource = ConnectionManager::getDataSource($this->_dataSourceName);
		$dataSource->execute($sql);

		$deleted = $dataSource->affectedRows() > 0;

		// trigger callback
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
		if (isset($this->_fieldMap[$field])) {
			$type = $this->_fieldMap[$field];
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
				return "'" . ConnectionManager::getDataSource($this->_dataSourceName)->escape($value) . "'";
		}
	}

	// ---------------------------------------------
	//  Callbacks
	// ---------------------------------------------

	/**
	 * Model::_beforeSave()
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

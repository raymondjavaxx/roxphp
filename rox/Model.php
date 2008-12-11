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
class Model extends Object {

	/**
	 * Model name
	 *
	 * @var string
	 */   	
	protected $_name = '';

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_table = '';

	/**
	 * Primary key
	 *
	 * @var string
	 */
	protected $_primaryKey = 'id';

	/**
	 * Object ID
	 *
	 * @var mixed
	 */
	protected $_id = null;

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

	protected $_modifiedFields = array();

	/**
	 * ID setter
	 *
	 * @param mixed $id 
	 */
	public function setId($id) {
		$this->_id = $id;
	}

	/**
	 * ID getter
	 *
	 * @return mixed $id 
	 */
	public function getId() {
		return $this->_id;
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
	 * Model::_resetModifiedFields()
	 *
	 * @return void
	 */
	protected function _resetModifiedFields() {
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
			$this->_data = $what;
		} else {
			$this->_data[$what] = $value;
			$this->_flagFieldAsModified($what);
		}
	}

	/**
	 * Adds data without replacing the existing data 
	 *
	 * @param array $data
	 */
	public function addData($data) {
		foreach ($data as $f => $v) {
			$this->setData($f, $v);
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
	 * Resets the model data and ID
	 *
	 * @param mixed $data
	 */
	public function create($data) {
		$this->_resetModifiedFields();
		$this->setId(null);
		$this->setData($data);
		return $this;
	}

	/**
	 * Model::save()
	 *
	 * @return boolean
	 */
	public function save() {
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

		$DataSource = DataSource::getInstance();

		if (empty($this->_id) || !$this->exists($this->_id)) {
			foreach($data as $f => $v) {
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

			$DataSource->execute($sql);
			if ($DataSource->affectedRows() == 1) {
				$this->_resetModifiedFields();
				$this->setId($DataSource->lastInsertedID());
				$this->_afterSave(true);
				return true;
			}
		} else {
			$updateData = array();
			foreach($data as $f => $v) {
				$updateData[] = '`' . $f . '` = ' . $this->smartQuote($f, $v);
			}

			$sql = sprintf(
				"UPDATE `%s` SET %s WHERE `%s` = %s",
				$this->_table,
				implode(', ', $updateData),
				$this->_primaryKey,
				$this->smartQuote($this->_primaryKey, $this->_id)
			);

			if ($DataSource->execute($sql) !== false) {
				$this->_resetModifiedFields();
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

		$result = DataSource::getInstance()->query($sql);
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

		$result = DataSource::getInstance()->query($sql);
		if (empty($result)) {
			return false;
		}

		$this->setId($id);
		$this->setData($result[0]);

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

		$sql = sprintf("SELECT %s FROM `%s` WHERE", $fields, $this->_table);

		if (empty($conditions)) {
			$conditions = array('1 = 1');
		}

		if (is_string($conditions)) {
			$sql .= ' ' . $conditions;
		} else {
			$_conditions = array();
			foreach($conditions as $f => $v) {
				if (is_int($f)) {
					$_conditions[] = ' ' . $v;
				} else {
					$_conditions[] = ' `' . $f . '` = ' . $this->smartQuote($f, $v);
				}
			}

			$sql .= implode(' AND ', $_conditions);
		}

		if (!empty($order)) {
			$sql .= ' ORDER BY ' . $order;
		}

		if (!empty($limit)) {
			$sql .= ' LIMIT ' . $limit;
		}

		$DataSource = DataSource::getInstance();
		return $DataSource->query($sql);
	}

	/**
	 * Model::find()
	 *
	 * @param mixed $fields
	 * @param mixed $conditions
	 * @param string $order
	 * @return array
	 */
	public function find($fields = null, $conditions = array(), $order = null) {
		$result = $this->findAll($fields, $conditions, $order, '1');
		if (empty($result)) {
			return array();
		}

		return $result[0];
	}

	/**
	 * Deletes a record
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function delete($id = null) {
		if (empty($id)) {
			$id = $this->_id;
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

		$DataSource = DataSource::getInstance();
		$DataSource->execute($sql);

		$deleted = $DataSource->affectedRows() > 0;

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
				return "'" . DataSource::getInstance()->escape($value) . "'";
		}
	}

	// ---------------------------------------------
	//  Callbacks
	// ---------------------------------------------

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
<?php
/**
 * Model
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Model extends Object {

	public $name = '';
	protected $table = '';
	protected $primaryKey = 'id';
	protected $id = null;
	protected $data = array();
	protected $fieldMap = array('id' => array('type' => 'integer'));

  /**
   * ID setter
   *
   * @param mixed $id 
   */
	public function setId($id) {
		$this->id = $id;
	}

  /**
   * ID getter
   *
   * @return mixed $id 
   */
	public function getId() {
		return $this->id;
	}

  /**
   * Set data
   *
   * @param string|array $what
   * @param mixed $value 
   */
	public function setData($what, $value = null) {
		if (is_array($what)) {
			$this->data = $what;
		} else {
			$this->data[$what] = $value;
		}
	}

  /**
   * Adds data without replacing the existing data 
   *
   * @param array $data
   */
	function addData($data) {
		foreach($data as $f => $v) {
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
			return $this->data;
		}

		if (array_key_exists($what, $this->data)) {
			return $this->data[$what];
		}

		return null;
	}

  /**
   * Resets the model data and ID
   *
   * @param mixed $data
   */
	function create($data) {
		$this->setId(null);
		$this->setData($data);
	}

  /**
   * Model::save()
   *
   * @return boolean
   */
	function save() {
		$data = $this->getData();
		if (empty($data)) {
			return false;
		}

		unset($data[$this->primaryKey]);

		$DataSource = DataSource::getInstance();

		if (empty($this->id) || !$this->exists($this->id)) {
			foreach($data as $f => $v) {
				$data[$f] = $this->smartQuote($f, $v);
			}

			$fields = '`' . implode('`, `', array_keys($data)) . '`';
			$values = implode(', ', array_values($data));

			$sql = sprintf(
				"INSERT INTO `%s` (%s) VALUES (%s)",
				$this->table,
				$fields,
				$values
			);

			$DataSource->execute($sql);
			$saved = $DataSource->affectedRows() == 1;
			if ($saved) {
				$this->setId($DataSource->lastInsertedID());
				return true;
			}
		} else {
			$updateData = array();
			foreach($data as $f => $v) {
				$updateData[] = '`' . $f . '` = ' . $this->smartQuote($f, $v);
			}

			$sql = sprintf(
				"UPDATE `%s` SET %s WHERE `%s` = %s",
				$this->table,
				implode(', ', $updateData),
				$this->primaryKey,
				$this->smartQuote($this->primaryKey, $this->id)
			);

			return $DataSource->execute($sql) !== FALSE;
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
			$this->table,
			$this->primaryKey,
			$this->smartQuote($this->primaryKey, $id)
		);

		$result = DataSource::getInstance()->query($sql);
		return $result[0]['count'] == 1;
	}

  /**
   * Model::read()
   *
   * @param mixed $id 
   * @param mixed $fields
   * @return boolean
   */
	public function read($id = null, $fields = null) {
		if (empty($fields)) {
			$fields = '*';
		} else if (is_array($fields)) {
			$fields = '`' . implode('`, `', $fields) . '`';
		}

		$sql = sprintf(
			"SELECT %s FROM `%s` WHERE `%s` = %s",
			$fields,
			$this->table,
			$this->primaryKey,
			$this->smartQuote($this->primaryKey, $id)
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

		$sql = sprintf("SELECT %s FROM `%s` WHERE", $fields, $this->table);

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
			$id = $this->id;
		}

		if (empty($id)) {
			return false;
		}

		$sql = sprintf(
			"DELETE FROM `%s` WHERE `%s` = %s",
			$this->table,
			$this->primaryKey,
			$this->smartQuote($this->primaryKey, $id)
		);

		$DataSource = DataSource::getInstance();
		$DataSource->execute($sql);
		return $DataSource->affectedRows() > 0;
	}

  /**
   * Quotes and escapes values to be used in SQL queries  
   *
   * @param string $field
   * @param mixed $value
   * @return mixed
   */
	public function smartQuote($field, $value) {
		$type = 'string';
		if (isset($this->fieldMap[$field]['type'])) {
			$type = $this->fieldMap[$field]['type'];
		}

		if ($type == 'string') {
			return "'" . DataSource::getInstance()->escape($value) . "'";
		} else if($type == 'integer') {
			return (integer)$value;
		}

		return DataSource::getInstance()->escape($value);
	}
}
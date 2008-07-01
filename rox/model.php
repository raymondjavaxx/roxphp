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

  /**
   * Object name
   *
   * @var string
   */   	
	public $name = '';

  /**
   * Table name
   *
   * @var string
   */
	protected $table = '';

  /**
   * Primary key
   *
   * @var string
   */
	protected $primaryKey = 'id';

  /**
   * Object ID
   *
   * @var mixed
   */
	protected $id = null;

  /**
   * Object data
   *
   * @var array
   */
	protected $data = array();

  /**
   * Field map
   *
   * @var array
   */
	protected $fieldMap = array(
		'id' => DATATYPE_INTEGER
	);

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
	public function addData($data) {
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
	public function create($data) {
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
			if ($DataSource->affectedRows() == 1) {
				$this->setId($DataSource->lastInsertedID());
				$this->afterSave(true);
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

			if ($DataSource->execute($sql) !== FALSE) {
				$this->afterSave(false);
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
			$this->table,
			$this->primaryKey,
			$this->smartQuote($this->primaryKey, $id)
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

		$deleted = $DataSource->affectedRows() > 0;

		// trigger callback
		if ($deleted) {
			$this->afterDelete();
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

		if (isset($this->fieldMap[$field])) {
			$type = $this->fieldMap[$field];
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
	protected function afterSave($created) {
	}

	/**
	 * After delete callback
	 *
	 */
	protected function afterDelete() {
	}
}
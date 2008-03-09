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
	var $name = null;
	var $table = null;
	var $primaryKey = 'id';
	var $data = null;
	var $id = null;

	private $datasource = null;

  /**
   * Class constructor
   *
   * @return
   */
	function __construct() {
		$this->datasource = Registry::getObject('DataSource');
	}

  /**
   * Resets the model data
   *
   * @param mixed $data
   * @return
   */
	function create($data) {
		//reset the id
		$this->id = null;
		$this->data = $data;
	}

  /**
   * Model::save()
   *
   * @param mixed $data
   * @return
   */
	function save($data = null) {
		if (!empty($data)) {
			$this->data = $data;
		}

		$values = array_values($this->data[$this->name]);

		$total = count($values);
		for($i=0; $i<$total; $i++) {
			$values[$i] = '\'' . $this->datasource->escape($values[$i]) . '\'';
		}

		$fields = implode(', ', array_keys($this->data));
		$values = implode(', ', $values);

		$this->datasource->execute("INSERT INTO `{$this->table}` ({$fields}) VALUES ({$values})");

		$this->id = $this->datasource->lastInsertedID();
	}

  /**
   * Model::read()
   *
   * @param mixed $fields
   * @param mixed $id
   * @return
   */
	function read($fields = null, $id = null) {
		if (empty($fields)) {
			$fields = '*';
		} else if (is_array()) {
			$fields = implode(', ', $fields);
		}

		$sql = "SELECT {$fields} FROM `{$this->table}` WHERE `{$this->primaryKey}` = {$id}";
		return $this->datasource->query($sql);
	}

  /**
   * Model::delete()
   *
   * @param mixed $id
   * @return
   */
	function delete($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		if (empty($id)) {
			return false;
		}

		$this->datasource->execute("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = {$id}");
	}

  /**
   * Model::query()
   *
   * @param mixed $sql
   * @return
   */
	function query($sql) {
		return $this->datasource->query($sql); 
	}
}
?>
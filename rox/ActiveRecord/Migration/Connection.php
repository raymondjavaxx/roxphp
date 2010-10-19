<?php

class Rox_ActiveRecord_Migration_Connection {

	protected static $_typeMap = array(
		'binary'    => array('native_type' => 'BLOB'),
		'boolean'   => array('native_type' => 'TINYINT', 'len' => 1),
		'date'      => array('native_type' => 'DATE'),
		'datetime'  => array('native_type' => 'DATETIME'),
		'decimal'   => array('native_type' => 'DECIMAL'),
		'float'     => array('native_type' => 'FLOAT'),
		'integer'   => array('native_type' => 'INTEGER', 'len' => 11),
		'string'    => array('native_type' => 'VARCHAR', 'len' => 255),
		'text'      => array('native_type' => 'TEXT'),
		'time'      => array('native_type' => 'TIME'),
		'timestamp' => array('native_type' => 'DATETIME'),
	);

	public function createTable($tableName, $options = array()) {
		$operation = new Rox_ActiveRecord_Migration_CreateTableOperation($tableName, $options);
		return $operation;
	}

	public function addColumn($tableName, $columnName, $type, $options = array()) {
		$sql = sprintf("ALTER TABLE `%s` ADD `%s` %s",
			$tableName, $columnName, self::expandColumn($type, $options));
		$this->_datasource()->execute($sql);
	}

	public function renameColumn($tableName, $columnName, $newColumnName) {
		$columnInfo = $this->_columnInfo($tableName, $columnName);
		$sql = sprintf("ALTER TABLE `%s` CHANGE `%s` `%s` %s",
			$tableName, $columnName, $newColumnName, strtoupper($columnInfo['type']));
		$this->_datasource()->execute($sql);
	}

	public function removeColumn($tableName, $columnName) {
		$sql = sprintf("ALTER TABLE `%s` DROP `%s`", $tableName, $columnName);
		$this->_datasource()->execute($sql);
	}

	public function createDatabase($name) {
		$this->_datasource()->execute("CREATE DATABASE `{$name}`");
	}

	public function dropDatabase($name) {
		$this->_datasource()->execute("DROP DATABASE `{$name}`");
	}

	protected function _columnInfo($table, $column) {
		$sql = sprintf("SHOW COLUMNS FROM `%s` LIKE '%s'", $table, $column);
		$rows = $this->_datasource()->query($sql);
		if (empty($rows)) {
			throw new Rox_Exception("Column '{$table}.{$column}' doesn't exist");
		}

		list($row) = $rows;
		$row = array_change_key_case($row, CASE_LOWER);
		return $row;
	}

	protected function _datasource() {
		return Rox_ConnectionManager::getDataSource();
	}

	public static function expandColumn($type, $options = array()) {
		if (!isset(self::$_typeMap[$type])) {
			throw new Rox_Exception("Unknown type {$type}");
		}

		$defaults = array('null' => true);
		$definition = array_merge(self::$_typeMap[$type], $defaults, $options);

		$result = array();
		$result[] = $definition['native_type'];

		if (array_key_exists('len', $definition)) {
			$result[] = "({$definition['len']})";
		}

		$result[] = $definition['null'] ? ' NULL' : ' NOT NULL';
		return implode('', $result);
	}
}

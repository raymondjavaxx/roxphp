<?php

class Rox_ActiveRecord_Migration_Connection {

	public function createTable($tableName, $options = array()) {
		$operation = new Rox_ActiveRecord_Migration_CreateTableOperation($tableName, $options);
		return $operation;
	}

	public function addColumn($tableName, $columnName, $type, $options = array()) {
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
}

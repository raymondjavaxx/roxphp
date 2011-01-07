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
 * Rox_ActiveRecord_Migration_Connection
 *
 * @package Rox
 */
class Rox_ActiveRecord_Migration_Connection {

	protected static $_typeMap = array(
		'binary'    => array('native_type' => 'BLOB'),
		'boolean'   => array('native_type' => 'TINYINT', 'len' => 1, 'null' => false, 'default' => false),
		'date'      => array('native_type' => 'DATE'),
		'datetime'  => array('native_type' => 'DATETIME'),
		'decimal'   => array('native_type' => 'DECIMAL', 'len' => '10,2'),
		'float'     => array('native_type' => 'FLOAT'),
		'integer'   => array('native_type' => 'INTEGER', 'len' => 11),
		'string'    => array('native_type' => 'VARCHAR', 'len' => 255),
		'text'      => array('native_type' => 'TEXT'),
		'time'      => array('native_type' => 'TIME'),
		'timestamp' => array('native_type' => 'DATETIME'),
	);

	public function createTable($tableName, $options = array()) {
		$this->_log(" # creating table {$tableName}");
		$operation = new Rox_ActiveRecord_Migration_CreateTableOperation($tableName, $options);
		return $operation;
	}

	public function dropTable($tableName) {
		$this->_log(" # dropping table {$tableName}");
		$this->_datasource()->execute("DROP TABLE `{$tableName}`");
	}

	public function addColumn($tableName, $columnName, $type, $options = array()) {
		$this->_log(" # adding column {$tableName}.{$columnName}");

		$sql = sprintf("ALTER TABLE `%s` ADD `%s` %s",
			$tableName, $columnName, self::expandColumn($type, $options));
		$this->_datasource()->execute($sql);
	}

	public function renameColumn($tableName, $columnName, $newColumnName) {
		$this->_log(" # renaming column {$tableName}.{$columnName} to {$tableName}.{$newColumnName}");

		$columnInfo = $this->_columnInfo($tableName, $columnName);
		$sql = sprintf("ALTER TABLE `%s` CHANGE `%s` `%s` %s",
			$tableName, $columnName, $newColumnName, strtoupper($columnInfo['type']));
		$this->_datasource()->execute($sql);
	}

	public function removeColumn($tableName, $columnName) {
		$this->_log(" # removing column {$tableName}.{$columnName}");

		$sql = sprintf("ALTER TABLE `%s` DROP `%s`", $tableName, $columnName);
		$this->_datasource()->execute($sql);
	}

	public function createDatabase($name) {
		$this->_log(" # creating database {$name}");

		$this->_datasource()->execute("CREATE DATABASE `{$name}`");
	}

	public function dropDatabase($name) {
		$this->_log(" # dropping database {$name}");

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

	protected function _log($text) {
		echo $text . "\n";
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

		if (array_key_exists('default', $definition)) {
			switch (gettype($definition['default'])) {
				case 'NULL':
					$result[] = " DEFAULT NULL";
					break;
				case 'boolean':
					$default = $definition['default'] ? '1' : '0';
					$result[] = " DEFAULT '{$default}'";
					break;
				default:
					$result[] = " DEFAULT '{$definition['default']}'";
					break;
			}
		}

		return implode('', $result);
	}
}

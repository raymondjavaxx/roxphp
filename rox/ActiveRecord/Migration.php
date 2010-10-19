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
 * Migration base class
 *
 * @package Rox
 */
class Rox_ActiveRecord_Migration {

	protected $_connection;

	public function __construct() {
		$this->_connection = new Rox_ActiveRecord_Migration_Connection;
	}

	public function up() {
	}

	public function down() {
	}

	public function createTable($tableName, $options = array()) {
		echo " # creating table {$tableName}\n";
		return $this->_connection->createTable($tableName, $options);
	}

	public function dropTable($tableName) {
		echo " # dropping table {$tableName}\n";
		return $this->_connection->dropTable($tableName);
	}

	public function addColumn($tableName, $columnName, $type, $options = array()) {
		echo " # adding column {$tableName}.{$columnName}\n";
		$this->_connection->addColumn($tableName, $columnName, $type, $options);
	}

	public function renameColumn($tableName, $columnName, $newColumnName) {
		echo " # renaming column {$tableName}.{$columnName} to {$tableName}.{$newColumnName}\n";
		$this->_connection->renameColumn($tableName, $columnName, $newColumnName);
	}

	public function removeColumn($tableName, $columnName) {
		echo " # removing column {$tableName}.{$columnName}\n";
		$this->_connection->removeColumn($tableName, $columnName);
	}

	public function createDatabase($name) {
		echo " # creating database {$name}\n";
		$this->_connection->createDatabase($name);
	}

	public function dropDatabase($name) {
		echo " # dropping database {$name}\n";
		$this->_connection->dropDatabase($name);
	}
}

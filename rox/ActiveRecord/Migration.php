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
		$this->_connection = new Rox_ActiveRecord_Migration_ConnectionProxy($this->_connection);
		$this->up();
		$this->_connection->playReversed();
	}

	public function createTable($tableName, $options = array()) {
		return $this->_connection->createTable($tableName, $options);
	}

	public function dropTable($tableName) {
		return $this->_connection->dropTable($tableName);
	}

	public function addColumn($tableName, $columnName, $type, $options = array()) {
		$this->_connection->addColumn($tableName, $columnName, $type, $options);
	}

	public function renameColumn($tableName, $columnName, $newColumnName) {
		$this->_connection->renameColumn($tableName, $columnName, $newColumnName);
	}

	public function removeColumn($tableName, $columnName) {
		$this->_connection->removeColumn($tableName, $columnName);
	}

	public function createDatabase($name) {
		$this->_connection->createDatabase($name);
	}

	public function dropDatabase($name) {
		$this->_connection->dropDatabase($name);
	}
}

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

	/**
	 * Connection
	 *
	 * @var Rox_ActiveRecord_Migration_Connection
	 */
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

	/**
	 * Returns a CreateTableOperation object for creating a table
	 *
	 * @param string $tableName name of table to be created
	 * @param array $options
	 *         - engine: Storage engine for table (optional, defaults to InnoDB)
	 * @return Rox_ActiveRecord_Migration_CreateTableOperation
	 * @see Rox_ActiveRecord_Migration_CreateTableOperation
	 */
	public function createTable($tableName, $options = array()) {
		return $this->_connection->createTable($tableName, $options);
	}

	/**
	 * Drops a given database table
	 *
	 * @param string $tableName 
	 * @return void
	 */
	public function dropTable($tableName) {
		return $this->_connection->dropTable($tableName);
	}

	/**
	 * Adds a column to a database table
	 *
	 * @param string $tableName name of the target table
	 * @param string $columnName name of column to be created
	 * @param string $type type of table
	 *         Possible values: string, text, integer, time, float, decimal,
	 *                          datetime, timestamp, date, boolean, binary
	 * @param array $options 
	 * @return void
	 */
	public function addColumn($tableName, $columnName, $type, $options = array()) {
		$this->_connection->addColumn($tableName, $columnName, $type, $options);
	}

	/**
	 * Renames a database column
	 *
	 * @param string $tableName  target table
	 * @param string $columnName  name of column to be renamed
	 * @param string $newColumnName  new name for column
	 * @return void
	 */
	public function renameColumn($tableName, $columnName, $newColumnName) {
		$this->_connection->renameColumn($tableName, $columnName, $newColumnName);
	}

	/**
	 * Removes(drops) a column from database
	 *
	 * @param string $tableName
	 * @param string $columnName 
	 * @return void
	 */
	public function removeColumn($tableName, $columnName) {
		$this->_connection->removeColumn($tableName, $columnName);
	}

	/**
	 * Creates a new database
	 *
	 * @param string $name 
	 * @return void
	 */
	public function createDatabase($name) {
		$this->_connection->createDatabase($name);
	}

	/**
	 * Drops a database
	 *
	 * @param string $name 
	 * @return void
	 */
	public function dropDatabase($name) {
		$this->_connection->dropDatabase($name);
	}

	/**
	 * Creates a new index
	 *
	 * @param string $tableName 
	 * @param string|array $columnNames 
	 * @param array $options
	 *         - name: name for index (defaults to idx_[colum_names])
	 *         - unique: whether or not index is unique
	 * @return void
	 */
	public function addIndex($tableName, $columnNames, $options = array()) {
		$this->_connection->addIndex($tableName, $columnNames, $options);
	}

	/**
	 * Removes(drops) an index
	 *
	 * @param string $tableName 
	 * @param string|array $columnNames 
	 * @param array $options
	 *         - name: name of index (defaults to idx_[colum_names])
	 * @return void
	 */
	public function removeIndex($tableName, $columnNames, $options = array()) {
		$this->_connection->removeIndex($tableName, $columnNames, $options);
	}
}

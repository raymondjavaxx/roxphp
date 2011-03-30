<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\active_record\migration;

use \rox\active_record\ConnectionManager;
use \rox\Exception;

/**
 * Encapsulates the logic for creating tables
 *
 * @package Rox
 */
class CreateTableOperation {

	protected $_tableName;
	protected $_options = array('engine' => 'InnoDB');
	protected $_columns;

	/**
	 * Constructor
	 *
	 * @param string $tableName 
	 * @param array $options 
	 *         - engine: Storage engine for table (optional, defaults to InnoDB)
	 */
	public function __construct($tableName, $options = array()) {
		$this->_tableName = $tableName;
		$this->_options = array_merge($this->_options, $options);
	}

	public function column($name, $type, $options = array()) {
		$this->_columns[] = compact('name', 'type', 'options');
	}

	public function __call($method, $args = array()) {
		if (count($args) < 1) {
			throw new Exception("Missing name argument");
		}

		$name = $args[0];
		$type = $method;
		$options = isset($args[1]) ? $args[1] : array();

		$this->column($name, $type, $options);
	}

	public function timestamps() {
		$this->datetime('created_at', array('null' => false));
		$this->datetime('updated_at', array('null' => false));
	}

	public function finish() {
		$colsDef = array();
		$colsDef[] = "`id` INTEGER(11) UNSIGNED NOT NULL AUTO_INCREMENT";

		foreach ($this->_columns as $column) {
			$expanded = Connection::expandColumn($column['type'], $column['options']);
			$colsDef[] = sprintf("`%s` %s", $column['name'], $expanded);
		}

		$colsDef[] = "PRIMARY KEY (`id`)";

		$sql = array();
		$sql[] = sprintf("CREATE TABLE `%s` (", $this->_tableName);
		$sql[] = implode(",\n", $colsDef);
		$sql[] = sprintf(") ENGINE %s;", $this->_options['engine']);
		$sql = implode("\n", $sql);

		$datasource = ConnectionManager::getDataSource();
		$datasource->execute($sql);
	}
}

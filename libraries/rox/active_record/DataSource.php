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

namespace rox\active_record;

/**
 * DataSource
 *
 * @package Rox
 */
class DataSource {

	/**
	 * DB Link Identifier
	 * 
	 * @var resource
	 */
	protected $_link = null;

	/**
	 * Last result
	 *
	 * @var resource
	 */
	protected $_result = null;

	/**
	 * Default connection settings
	 *
	 * @var array
	 */
	protected $_settings = array(
		'host'     => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'database' => ''
	);

	/**
	 * Class Constructor
	 *
	 * @param array $settings
	 * @return void
	 */
	public function __construct($settings) {
		$this->_settings = array_merge($this->_settings, $settings);
	}

	/**
	 * Connects to database server
	 *
	 * @return void
	 * @throws Exception
	 */
	public function connect() {
		$this->_link = mysql_connect($this->_settings['host'],
			$this->_settings['username'], $this->_settings['password']);

		if (false === $this->_link) {
			throw new Exception('Could not connect to DB server - ' . mysql_error());
		}

		if (mysql_select_db($this->_settings['database'], $this->_link) == false) {
			throw new Exception('Could not select DB - ' . mysql_error($this->_link));
		}
	}

	/**
	 * Disconects from the database server
	 *
	 * @return boolean
	 */
	public function disconnect() {
		return mysql_close($this->_link);
	}

	/**
	 * Lists all the tables of the current database
	 *
	 * @return array
	 */
	public function listTables() {
		$result = $this->execute('SHOW TABLES');
		if (!$result) {
			return array();
		}

		$tables = array();
		while ($row = mysql_fetch_row($result)) {
			$tables[] = $row[0];
		}

		return $tables;
	}

	/**
	 * DataSource::describe()
	 *
	 * @param mixed $table
	 * @return array
	 */
	public function describe($table) {
		return $this->query('DESCRIBE ' . $table);
	}

	/**
	 * DataSource::generateFieldMapFromTable()
	 * 
	 * @param string $table
	 * @return array
	 */
	public function generateAttributeMapFromTable($table) {
		$fieldMap = array();

		$columns = $this->describe($table);
		foreach ($columns as $col) {
			$type = strtolower($col['Type']);
			$name = $col['Field'];

			if ($type == 'tinyint(1)') {
				$type = 'boolean';
			} else if (strpos($col['Type'], 'int') !== false) {
				$type = 'integer';
			} else if (strpos($col['Type'], 'char') !== false || $col['Type'] == 'text') {
				$type = 'string';
			} else if(preg_match('/^decimal|float|double/', $col['Type']) === 1) {
				$type = 'float';
			} else if($type == 'blob') {
				$type = 'binary';
			}

			$fieldMap[$name] = $type;
		}

		return $fieldMap;
	}

	/**
	 * Escapes a string for use in a query
	 *
	 * @param string $value
	 * @return string
	 */
	public function escape($value) {
		return mysql_real_escape_string($value, $this->_link);
	}

	/**
	 * Performs a SQL query and returns the fetched results
	 *
	 * @param string $sql
	 * @return array
	 */
	public function query($sql) {
		$this->_result = $this->execute($sql);
		if (!$this->_result) {
			return array();
		}

		return $this->fetchAll();
	}

	/**
	 * Fetches results of the last query
	 *
	 * @return array
	 */
	public function fetchAll() {
		$data = array();
		while ($_data = mysql_fetch_assoc($this->_result)) {
			$data[] = $_data;
		}

		return $data;
	}

	/**
	 * Performs a raw SQL query
	 *
	 * @param string $sql
	 * @return resource
	 * @throws Exception
	 */
	public function execute($sql) {
		$result = mysql_query($sql, $this->_link);
		if ($result === false) {
			throw new Exception(mysql_error($this->_link), mysql_errno($this->_link));
		}
		return $result;
	}

	/**
	 * Begin a transaction
	 *
	 * @return boolean
	 */
	public function beginTransaction() {
		return $this->execute('BEGIN');
	}

	/**
	 * Commit a transaction
	 *
	 * @return boolean
	 */
	public function commitTransaction() {
		return $this->execute('COMMIT');
	}

	/**
	 * Rollback a transaction
	 *
	 * @return boolean
	 */
	public function rollbackTransaction() {
		return $this->execute('ROLLBACK');
	}

	/**
	 * DataSource::lastInsertedID()
	 *
	 * @return mixed
	 */
	public function lastInsertedID() {
		$result = $this->query('SELECT LAST_INSERT_ID() AS `id`');
		return $result[0]['id'];
	}

	/**
	 * Returns the number of the affected rows in previous operation
	 *
	 * @return integer
	 */
	public function affectedRows() {
		return mysql_affected_rows($this->_link);
	}
}

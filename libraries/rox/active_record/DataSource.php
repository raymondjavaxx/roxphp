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

use \PDO;
use \PDOException;

/**
 * DataSource
 *
 * @package Rox
 */
class DataSource {

	/**
	 * Database connection
	 * 
	 * @var \DBO
	 */
	protected $connection;

	/**
	 * Default connection settings
	 *
	 * @var array
	 */
	protected $_settings = array(
		'host'     => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'database' => '',
		'charset'  => 'utf8'
	);

	public function dsn() {
		$database = $this->_settings['database'];
		$host     = $this->_settings['host'];
		$charset  = $this->_settings['charset'];
		return sprintf("mysql:dbname=%s;host=%s;charset=%s", $database, $host, $charset);
	}

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
		$username = $this->_settings['username'];
		$password = $this->_settings['password'];

		try {
			$this->connection = new PDO($this->dsn(), $username, $password);	
		} catch (PDOException $e) {
			throw new Exception('Connection failed: ' . $e->getMessage());
		}
	}

	/**
	 * Disconects from the database server
	 *
	 * @return boolean
	 */
	public function disconnect() {
		if (is_object($this->connection)) {
			$this->connection = null;
			return true;
		}

		return false;
	}

	/**
	 * Lists all the tables of the current database
	 *
	 * @return array
	 */
	public function listTables() {
		$results = $this->connection->query('SHOW TABLES');
		if (!$results) {
			return array();
		}

		$tables = array();
		foreach ($results as $row) { 
			$tables[] = $row[0];
		}

		return $tables;
	}

	/**
	 * DataSource::describe()
	 *
	 * @param string $table
	 * @return array
	 */
	public function describe($table) {
		return $this->query(sprintf('DESCRIBE `%s`', $table));
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
		return $this->connection->quote($value);
	}

	/**
	 * Performs a SQL query and returns the fetched results
	 *
	 * @param string $sql
	 * @return array
	 */
	public function query($sql) {
		$res = $this->connection->query($sql);
		if ($res === false) {
			return array();
		}

		$rows = array();
		foreach ($res as $row) {
			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * Performs a raw SQL query
	 *
	 * @param string $sql
	 * @return integer
	 * @throws Exception
	 */
	public function execute($sql) {
		$result = $this->connection->exec($sql);

		if ($result === false) {
			$info = $this->connection->errorInfo();
			throw new Exception($info[2], $info[1]);
		}

		return $result;
	}

	/**
	 * Begin a transaction
	 *
	 * @return boolean
	 */
	public function beginTransaction() {
		return $this->connection->beginTransaction();
	}

	/**
	 * Commit a transaction
	 *
	 * @return boolean
	 */
	public function commitTransaction() {
		return $this->connection->commit();
	}

	/**
	 * Rollback a transaction
	 *
	 * @return boolean
	 */
	public function rollbackTransaction() {
		return $this->connection->rollback();
	}

	/**
	 * DataSource::lastInsertedID()
	 *
	 * @return mixed
	 */
	public function lastInsertedID() {
		return $this->connection->lastInsertId();
	}
}

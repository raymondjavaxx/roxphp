<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * DataSource
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class DataSource extends Object {

	const DBMS_DATE_FORMAT     = 'Y-m-d';
	const DBMS_DATETIME_FORMAT = 'Y-m-d H:i:s';

	protected $_link = null;

	protected $result = null;

	/**
	 * Default connection settings
	 *
	 * @var array
	 */
	protected $_settings = array(
		'host'     => 'localhost',
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
	 * Connects to database server and selects the database
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
	 * Escapes a string for use in a query
	 *
	 * @param string $value
	 * @return string
	 */
	public function escape($value) {
		return mysql_real_escape_string($value);
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
	 */
	public function execute($sql) {
		return mysql_query($sql, $this->_link);
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
	 * Date formater
	 * 
	 * @param string $date
	 * @return string
	 */
	public function formatDate($date) {
		return date(self::DBMS_DATE_FORMAT, strtotime($date));
	}

	/**
	 * DateTime formater
	 * 
	 * @param string $dateTime
	 * @return string
	 */
	public function formatDateTime($dateTime) {
		return date(self::DBMS_DATETIME_FORMAT, strtotime($dateTime));
	}

	/**
	 * Returns the number of the affected rows in previous operation
	 *
	 * @return integer
	 */
	public function affectedRows() {
		return mysql_affected_rows();
	}
}

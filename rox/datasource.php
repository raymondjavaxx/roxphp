<?php
/**
 * DataSource
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class DataSource extends Object {

	const DBMS_DATE_FORMAT     = 'Y-m-d';
	const DBMS_DATETIME_FORMAT = 'Y-m-d h-i-s';

	public $queries = array();

	private $link = null;

	private $result = null;

  /**
   * Returns a singleton instance of DataSource
   *
   * @return DataSource
   */
	public static function &getInstance() {
		static $instance;
		if (!is_object($instance)) {
			$instance = new DataSource;
		}

		return $instance;
	}

  /**
   * Connects to a database server and selects the database
   *
   * @param string $server
   * @param string $username
   * @param string $password
   * @param string $database
   * @return boolean
   */
	public function connect($server, $username, $password, $database) {
		$this->link = mysql_connect($server, $username, $password);
		if ($this->link === FALSE) {
			return FALSE;
		}

		return mysql_select_db($database, $this->link);
	}

  /**
   * Disconects from the database server
   *
   * @return boolean
   */
	public function disconnect() {
		return mysql_close($this->link);
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
   * @return
   */
	public function describe($table) {
		return $this->query('DESCRIBE ' . $table);
	}

  /**
   * Escapes a string for use in a query
   *
   * @param string $sql
   * @return array
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
		$this->result = $this->execute($sql);
		if (!$this->result) {
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
		while ($_data = mysql_fetch_assoc($this->result)) {
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
		if (ROX_DEBUG) {
			$this->queries[] = $sql;
		}

		return mysql_query($sql, $this->link);
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
   * @param string
   * @return string
   */
	public function formatDate($date) {
		return date(DataSource::DBMS_DATE_FORMAT, strtotime($date));
	}

  /**
   * DateTime formater
   * 
   * @param string
   * @return string
   */
	public function formatDateTime($dateTime) {
		return date(DataSource::DBMS_DATETIME_FORMAT, strtotime($dateTime));
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
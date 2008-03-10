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

	private $link = null;

	private $result = null;

  /**
   * Connects to a database server and selects the database
   *
   * @param string $server
   * @param string $username
   * @param string $password
   * @param string $database
   * @return boolean
   */
	function connect($server, $username, $password, $database) {
		$this->link = mysql_connect($server, $username, $password);
		if($this->link === FALSE) {
			return FALSE;
		}

		return mysql_select_db($database, $this->link);
	}

  /**
   * Disconects from the database server
   *
   * @return boolean
   */
	function disconnect() {
		return mysql_close($this->link);
	}

  /**
   * Lists all the tables of the current database
   *
   * @return array
   */
	function listTables() {
		$result = $this->execute('SHOW TABLES');
		if (!$result) {
			return array();
		}

		$tables = array();
		while($row = mysql_fetch_array($result)) {
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
	function describe($table) {
		return $this->query('DESCRIBE ' . $table);
	}

  /**
   * Escapes a string for use in a query
   *
   * @param string $sql
   * @return array
   */
	function escape($value) {
		return mysql_escape_string($value);
	}

  /**
   * Performs a SQL query and returns the fetched results
   *
   * @param string $sql
   * @return array
   */
	function query($sql) {
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
	function fetchAll() {
		$data = array();
		while($_data = mysql_fetch_assoc($this->result)) {
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
	function execute($sql) {
		return mysql_query($sql, $this->link);
	}

  /**
   * DataSource::lastInsertedID()
   *
   * @return mixed
   */
	function lastInsertedID() {
		$result = $this->query('SELECT LAST_INSERT_ID() AS `id`');
		return $result[0]['id'];
	}
}
?>
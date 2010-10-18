<?php

class Rox_ActiveRecord_Migration_CreateTableOperation {

	protected $_tableName;
	protected $_options;

	public function __construct($tableName, $options = array()) {
		$this->_tableName = $tableName;
		$this->_options = $options;
	}

	public function finish() {
	}
}

<?php

namespace rox\test\mocks\active_record;

class DataSource {

	protected $_sequence = 0;

	protected $_tables = array(
		'users' => array(
			'first_name' => 'string',
			'last_name' => 'string',
			'email' => 'string',
			'password' => 'string',
			'created_at' => 'datetime',
			'updated_at' => 'datetime'
		)
	);

	public function connect() {
		return true;
	}

	public function generateAttributeMapFromTable($table) {
		return $this->_tables[$table];
	}

	public function escape($string) {
		return addslashes($string);
	}

	public function lastInsertedID() {
		return $this->_sequence += 1;
	}

	public function execute() {
		return true;
	}

	public function affectedRows() {
		return 1;
	}
}

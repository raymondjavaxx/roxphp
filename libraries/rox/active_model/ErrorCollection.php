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

namespace rox\active_model;

use \rox\Inflector;

/**
 * ErrorCollection
 *
 * @package Rox
 */
class ErrorCollection implements \Countable {

	/**
	 * undocumented variable
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * undocumented function
	 *
	 * @return integer
	 */
	public function count() {
		return count($this->_errors);
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 */
	public function clear() {
		$this->_errors = array();
	}

	/**
	 * undocumented function
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->_errors;
	}

	/**
	 * undocumented function
	 *
	 * @param string $field 
	 * @param string $errorMessage 
	 * @return void
	 */
	public function add($field, $errorMessage = null) {
		if ($errorMessage === null) {
			$errorMessage = Inflector::humanize($field) . ' is invalid';
		}

		$this->_errors[$field] = $errorMessage;
	}
}

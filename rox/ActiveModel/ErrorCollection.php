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
 * Rox_ActiveModel_ErrorCollection
 *
 * @package Rox
 */
class Rox_ActiveModel_ErrorCollection implements Countable {

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
			$errorMessage = Rox_Inflector::humanize($field) . ' is invalid';
		}

		$this->_errors[$field] = $errorMessage;
	}
}

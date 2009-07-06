<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Rox_ActiveRecord_ErrorCollection
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_ActiveRecord_ErrorCollection implements Countable {

	/**
	 * undocumented variable
	 *
	 * @var array
	 */
	private $_errors = array();

	/**
	 * undocumented variable
	 *
	 * @var array
	 */
	private $_data;

	/**
	 * undocumented function
	 *
	 * @param array $data 
	 */
	public function __construct(array &$data) {
		$this->_data = &$data;
	}

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

	/**
	 * undocumented function
	 *
	 * @param string $field 
	 * @param string $message 
	 * @return void
	 */
	public function addOnEmpty($field, $message = "This field can't be left blank") {
		if (empty($this->_data[$field])) {
			$this->add($field, $message);
		}
	}
}

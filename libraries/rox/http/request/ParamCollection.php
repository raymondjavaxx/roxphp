<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2012 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\http\request;

class ParamCollection implements \ArrayAccess {

	protected $_data = array();

	public function __construct($data) {
		$this->_data = $data;
	}

	public function all() {
		return $this->_data;
	}

	public function get($key, $default = null) {
		return isset($this->_data[$key]) ? $this->_data[$key] : $default;
	}

	public function set($key, $value) {
		$this->_data[$key] = $value;
	}

	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	public function offsetGet($offset) {
		return $this->get($offset);
	}

	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}
}

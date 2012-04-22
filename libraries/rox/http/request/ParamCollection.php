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

	public function offsetExists($offset) {
		return isset($this->_data[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}

	public function offsetSet($offset , $value) {
		throw new Exception("Collection is read-only");
	}

	public function offsetUnset($offset) {
		throw new Exception("Collection is read-only");
	}
}

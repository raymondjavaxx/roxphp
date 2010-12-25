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
 * Migration Proxy class
 *
 * @package Rox
 */
class Rox_ActiveRecord_Migration_RecordingProxy {

	protected $_target;

	protected $_calls = array();

	public function __construct($target = null) {
		$this->_target = $target;
	}

	public function __call($method, $args) {
		$this->_calls[] = compact('method', 'args');
	}

	public function calls() {
		return $this->_calls;
	}
}

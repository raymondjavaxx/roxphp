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

namespace rox\log;

/**
 * Abstract adapter for \rox\Log
 * 
 * @package Rox
 */
abstract class Adapter {

	protected $_config = array();

	public function __construct($config = array()) {
		$this->_config += $config;
	}

	abstract public function write($type, $message);
}

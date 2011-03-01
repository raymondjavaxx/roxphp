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

namespace rox;

/**
 * Logger class
 * 
 * @package Rox
 */
class Log {

	protected static $_config = array(
		'adapter' => '\rox\log\adapter\File'
	);

	protected static $_adapter;

	public static function init($config = array()) {
		self::$_config = ($config + self::$_config);
	}

	public static function write($type, $message) {
		self::_adapter()->write($type, $message);
	}

	protected static function _adapter() {
		if (self::$_adapter === null) {
			self::$_adapter = new self::$_config['adapter'](self::$_config);
		}
		return self::$_adapter;
	}
}

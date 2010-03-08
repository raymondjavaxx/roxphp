<?php
/**
 * RoxPHP
 *
 * Copyright (c) 2008 - 2009 Ramon Torres
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
 * Logger class
 * 
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Log {

	const ADAPTER_FILE = 'Rox_Log_Adapter_File';

	protected static $_config = array(
		'adapter' => self::ADAPTER_FILE
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

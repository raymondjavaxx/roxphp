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
 * File adapter for Rox_Log
 * 
 * @package Rox
 */
class Rox_Log_Adapter_File extends Rox_Log_Adapter {

	public function __construct($config = array()) {
		$defaults = array('path' => ROX_APP_PATH . '/tmp/logs');
		parent::__construct($config + $defaults);
	}

	public function write($type, $message) {
		$filepath = $this->_config['path'] . '/' . $type . '.log';
		$line = date('c') . ' - ' . $message . "\n";
		return file_put_contents($filepath, $line, FILE_APPEND);
	}
}

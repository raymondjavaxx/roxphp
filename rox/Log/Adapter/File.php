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
 * File adapter for Rox_Log
 * 
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
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

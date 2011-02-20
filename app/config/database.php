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
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox;

/*
 * Database configuration
 */
ConnectionManager::setConfig('default', array(
	'host'     => '127.0.0.1',
	'username' => 'root',
	'password' => '',
	'database' => 'rox_app'
));

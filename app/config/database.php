<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package App
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2010 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/*
 * Database configuration
 */
Rox_ConnectionManager::setConfig('default', array(
	'host'     => '127.0.0.1',
	'username' => 'root',
	'password' => '',
	'database' => 'rox_app'
));

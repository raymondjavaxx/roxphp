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
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use \rox\active_record\ConnectionManager;
use \Rox\Utils\Environment;

switch (Environment::get()) {
	/*
	case 'production':
		// Production database configuration
		ConnectionManager::setConfig('default', array(
			'host'     => '127.0.0.1',
			'username' => 'root',
			'password' => '',
			'database' => 'rox_app'
		));
		break;

	case 'staging':
		// Development database configuration
		ConnectionManager::setConfig('default', array(
			'host'     => '127.0.0.1',
			'username' => 'root',
			'password' => '',
			'database' => 'rox_app_stage'
		));
		break;

	case 'test':
		// Testing database configuration
		ConnectionManager::setConfig('default', array(
			'host'     => '127.0.0.1',
			'username' => 'root',
			'password' => '',
			'database' => 'rox_app_test'
		));
		break;
	*/

	default:
		// Development database configuration
		ConnectionManager::setConfig('default', array(
			'host'     => '127.0.0.1',
			'username' => 'root',
			'password' => '',
			'database' => 'rox_app_dev'
		));
		break;
}

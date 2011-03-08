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

namespace rox\active_record;

/**
 * ConnectionManager
 *
 * @package Rox
 */
class ConnectionManager {

	/**
	 * DataSource instances
	 *
	 * @var array
	 */
	protected static $_dataSources = array();

	/**
	 * DataSource configurations
	 *
	 * @var array
	 */
	protected static $_configs = array();

	/**
	 * Returns a singleton instance of a datasource
	 *
	 * @return \rox\active_record\DataSource
	 */
	public static function getDataSource($name = 'default') {
		if (!isset(self::$_dataSources[$name])) {
			self::_instantiateDataSource($name);
		}

		return self::$_dataSources[$name];
	}

	/**
	 * Set DataSource config
	 *
	 * @param string $name name of datasource
	 * @param array $config
	 * @return void
	 */
	public static function setConfig($name, $config) {
		$default = array('class' => '\rox\active_record\DataSource');
		self::$_configs[$name] = $config + $default;
	}

	/**
	 * Instantiates a datasource
	 *
	 * @param string $name
	 * @return void
	 * @throws \rox\active_record\Exception
	 */
	protected static function _instantiateDataSource($name) {
		if (!isset(self::$_configs[$name])) {
			throw new Exception('Configuration entry not found for ' . $name);
		}

		$class = self::$_configs[$name]['class'];
		$dataSource = new $class(self::$_configs[$name]);
		$dataSource->connect();

		self::$_dataSources[$name] = $dataSource;
	}
}

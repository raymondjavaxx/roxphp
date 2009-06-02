<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * ConnectionManager
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_ConnectionManager {

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
	 * @return Night_DataSource
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
		self::$_configs[$name] = $config;
	}

	/**
	 * Instantiates a datasource
	 *
	 * @param string $name
	 * @return void
	 * @throws Exception
	 */
	protected static function _instantiateDataSource($name) {
		if (!isset(self::$_configs[$name])) {
			throw new Exception('Configuration entry not found for ' . $name);
		}

		$dataSource = new Rox_DataSource(self::$_configs[$name]);
		$dataSource->connect();

		self::$_dataSources[$name] = $dataSource;
	}
}

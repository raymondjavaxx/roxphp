<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id:$
 */

/**
 * @see Cache_Adapter_Abstract
 */
require_once(ROX . 'cache' . DS . 'adapter' . DS . 'abstract.php');

/**
 * Cache
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Cache {

	const ADAPTER_FILE = 'Cache_Adapter_File';
	const ADAPTER_MEMCACHE = 'Cache_Adapter_Memcache';

	/**
	 * Cache adapter instance
	 *
	 * @var Cache_Adapter_Abstract
	 */
	private static $adapter;

	/**
	 * Initializes the cache
	 * 
	 * @param string $adapter
	 * @param array $options
	 * @return void
	 */
	public static function init($adapter, array $options) {
		self::loadAdapter($adapter);
		self::$adapter = new $adapter($options);
	}

	/**
	 * Load cache adapter class
	 * 
	 * @param string $name
	 * @throws Exception
	 */
	protected static function loadAdapter($name) {
		switch ($name) {
			case self::ADAPTER_MEMCACHE:
				require(ROX . 'cache' . DS . 'adapter' . DS . 'memcache.php');
			break;

			case self::ADAPTER_FILE:
				require(ROX . 'cache' . DS . 'adapter' . DS . 'file.php');
			break;

			default: throw new Exception('Invalid Cache adapter');
		}
	}

	/**
	 * Cache::write()
	 * 
	 * @param mixed $key
	 * @param mixed $data
	 * @param mixed $expires
	 * @return mixed
	 */
	public static function write($key, $data, $expires) {
		return self::$adapter->write($key, $data, $expires);
	}

	/**
	 * Cache::read()
	 * 
	 * @param mixed $key
	 * @return boolean
	 */
	public static function read($key) {
		return self::$adapter->read($key);
	}

	/**
	 * Cache::delete()
	 * 
	 * @param mixed $key
	 * @return boolean
	 */
	public static function delete($key) {
		return self::$adapter->delete($key);
	}
}
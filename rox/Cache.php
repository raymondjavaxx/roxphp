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
 * Cache static class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Cache {

	const ADAPTER_FILE     = 'Rox_Cache_Adapter_File';
	const ADAPTER_MEMCACHE = 'Rox_Cache_Adapter_Memcache';

	/**
	 * Cache adapter instance
	 *
	 * @var Cache_Adapter_Abstract
	 */
	private static $_adapter;

	/**
	 * Initializes the cache.
	 * 
	 * @param string $adapter
	 * @param array $options
	 * @return void
	 */
	public static function init($adapter, array $options = array()) {
		Rox_Loader::loadClass($adapter);
		self::$_adapter = new $adapter($options);
	}

	/**
	 * Saves data to the cache
	 * 
	 * @param string $key  The cache key
	 * @param mixed $data  Data to be saved
	 * @param integer|string $expires  Expiration time in seconds or strtotime() friendly format
	 * @return boolean
	 */
	public static function write($key, $data, $expires) {
		return self::$_adapter->write($key, $data, $expires);
	}

	/**
	 * Retrieves cached data for a given key
	 * 
	 * @param string $key The cache key
	 * @return mixed
	 */
	public static function read($key) {
		return self::$_adapter->read($key);
	}

	/**
	 * Deletes a cache entry
	 * 
	 * @param string $key The cache key
	 * @return boolean
	 */
	public static function delete($key) {
		return self::$_adapter->delete($key);
	}
}

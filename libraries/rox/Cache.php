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

namespace rox;

/**
 * Cache static class
 *
 * @package Rox
 */
class Cache {

	const ADAPTER_FILE     = '\rox\cache\adapter\File';
	const ADAPTER_MEMCACHE = '\rox\cache\adapter\Memcache';

	protected static $_config = array(
		'adapter' => self::ADAPTER_FILE
	);

	/**
	 * Cache adapter instance
	 *
	 * @var Cache_Adapter
	 */
	private static $_adapter;

	/**
	 * Initializes the cache class
	 *
	 * @param array $config
	 * @return void
	 */
	public static function init($config = array()) {
		self::$_config = ($config + self::$_config);
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
		return self::_adapter()->write($key, $data, $expires);
	}

	/**
	 * Retrieves cached data for a given key
	 * 
	 * @param string $key The cache key
	 * @return mixed
	 */
	public static function read($key) {
		return self::_adapter()->read($key);
	}

	/**
	 * Deletes a cache entry
	 * 
	 * @param string $key The cache key
	 * @return boolean
	 */
	public static function delete($key) {
		return self::_adapter()->delete($key);
	}

	protected static function _adapter() {
		if (self::$_adapter === null) {
			self::$_adapter = new self::$_config['adapter'](self::$_config);
		}
		return self::$_adapter;
	}
}

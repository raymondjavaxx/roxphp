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
 * Cache
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
	 * Initializes the cache
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
	 * Rox_Cache::write()
	 * 
	 * @param mixed $key
	 * @param mixed $data
	 * @param mixed $expires
	 * @return mixed
	 */
	public static function write($key, $data, $expires) {
		return self::$_adapter->write($key, $data, $expires);
	}

	/**
	 * Rox_Cache::read()
	 * 
	 * @param mixed $key
	 * @return boolean
	 */
	public static function read($key) {
		return self::$_adapter->read($key);
	}

	/**
	 * Rox_Cache::delete()
	 * 
	 * @param mixed $key
	 * @return boolean
	 */
	public static function delete($key) {
		return self::$_adapter->delete($key);
	}
}

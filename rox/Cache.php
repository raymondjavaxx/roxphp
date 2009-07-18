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
 * @version $Id$
 */

/**
 * @see Rox_Cache_Adapter_Abstract
 */
require_once(ROX.'Cache'.DS.'Adapter'.DS.'Abstract.php');

/**
 * Cache
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
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
		self::_loadAdapter($adapter);
		self::$_adapter = new $adapter($options);
	}

	/**
	 * Loads cache adapter class
	 * 
	 * @param string $name
	 * @throws Exception
	 */
	protected static function _loadAdapter($name) {
		switch ($name) {
			case self::ADAPTER_MEMCACHE:
				require ROX.'Cache'.DS.'Adapter'.DS.'Memcache.php';
			break;

			case self::ADAPTER_FILE:
				require ROX.'Cache'.DS.'Adapter'.DS.'File.php';
			break;

			default:
				throw new Exception('Invalid Cache adapter');
		}
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

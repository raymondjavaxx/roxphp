<?php
/**
 * RoxPHP
 *
 * Copyright (c) 2008 - 2010 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Abstract class for cache adapters
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
abstract class Rox_Cache_Adapter {

	protected $_config = array();

	public function __construct($config) {
		$this->_config += $config;
	}

	/**
	 * Saves data to the cache
	 * 
	 * @param string $key  The cache key
	 * @param mixed $data  Data to be saved
	 * @param integer|string $expires  Expiration time in seconds or strtotime() friendly format
	 * @return boolean
	 */
	abstract public function write($key, &$data, $expires);

	/**
	 * Retrieves cached data for a given key
	 * 
	 * @param string $key  The cache key
	 * @return mixed
	 */
	abstract public function read($key);

	/**
	 * Deletes a cache entry
	 * 
	 * @param string $key  The cache key
	 * @return boolean
	 */
	abstract public function delete($key);
}

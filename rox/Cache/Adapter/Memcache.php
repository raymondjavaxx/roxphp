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
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Memcache cache adapter
 *
 * @package Rox
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Cache_Adapter_Memcache extends Rox_Cache_Adapter_Abstract {

	/**
	 * Memcache hosts and ports
	 *
	 * @var array
	 */
	protected $_servers = array('localhost' => 11211);

	/**
	 * Memcache instance
	 *
	 * @var Memcache
	 */
	protected $_memcache;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct($options) {
		if (isset($options['servers'])) {
			$this->_servers = $options['servers'];
		}

		$this->_initialize();
	}

	/**
	 * Initializes the Memcache object
	 *
	 * @return void
	 */
	protected function _initialize() {
		if (!class_exists('Memcache', false)) {
			throw new Exception('This cache adapter requires the php_memcache extension');
		}

		$this->_memcache = new Memcache;
		foreach ($this->_servers as $host => $port) {
			$this->_memcache->addServer($host, $port);
		}
	}

	/**
	 * Saves data to the cache
	 * 
	 * @param string $key  The cache key
	 * @param mixed $data  Data to be saved
	 * @param integer|string $expires  Expiration time in seconds or strtotime() friendly format
	 * @return boolean
	 */
	public function write($key, &$data, $expires) {
		if (is_string($expires)) {
			$expires = strtotime($expires);
		} else {
			$expires = time()+$expires;
		}

		return $this->_memcache->set($key, $data, 0, $expires);
	}

	/**
	 * Retrieves cached data for a given key
	 * 
	 * @param string $key The cache key
	 * @return mixed
	 */
	public function read($key) {
		return $this->_memcache->get($key);
	}

	/**
	 * Deletes a cache entry
	 * 
	 * @param string $key The cache key
	 * @return boolean
	 */
	public function delete($key) {
		return $this->_memcache->delete($key);
	}
}

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

namespace rox\cache\adapter;

use \rox\Exception;

/**
 * Memcache cache adapter
 *
 * @package Rox
 */
class Memcache extends \rox\cache\Adapter {

	protected $_config = array(
		'servers' => array('127.0.0.1:11211')
	);

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
	public function __construct($config = array()) {
		parent::__construct($config);
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

		$this->_memcache = new \Memcache;
		foreach ($this->_config['servers'] as $server) {
			$parts = explode(':', $server);
			$this->_memcache->addServer($parts[0], isset($parts[1]) ? $parts[1] : 11211);
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
		$expires = is_string($expires) ? strtotime($expires) : time() + $expires;
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

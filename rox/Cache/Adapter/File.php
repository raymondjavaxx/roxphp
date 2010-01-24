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
 * Filesystem based cache adapter
 *
 * @package Rox
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Cache_Adapter_File extends Rox_Cache_Adapter {

	/**
	 * Constructor
	 *
	 * @param array $config
	 */
	public function __construct($config) {
		$defaults = array('path' => ROX_APP_PATH . '/tmp/cache');
		parent::__construct($config + $defaults);

		if (!is_dir($this->_config['path'])) {
			throw new Exception('Cache path does not exists');
		}
	}

	/**
	 * Stores data into the cache
	 * 
	 * @param string $key  The cache key
	 * @param mixed $data  Data to be saved
	 * @param integer|string $expires  Expiration time in seconds or strtotime() friendly format
	 * @return boolean
	 */
	public function write($key, &$data, $expires) {
		$expires = is_string($expires) ? strtotime($expires) : time() + $expires;
		$serializedData = serialize($data);

		$fp = fopen($this->_config['path'] . '/cache_' . sha1($key) . '.txt', 'w');
		flock($fp, LOCK_EX);
		fwrite($fp, $expires . "\n");
		fwrite($fp, strlen($serializedData) . "\n");
		fwrite($fp, $serializedData);
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	/**
	 * Retrieves cached data for a given key
	 * 
	 * @param string $key  The cache key
	 * @return mixed
	 */
	public function read($key) {
		$fp = @fopen($this->_config['path'] . '/cache_' . sha1($key) . '.txt', 'r');
		if ($fp === false) {
			return false;
		}

		flock($fp, LOCK_EX);
		$expires = (integer)fgets($fp, 20);
		if ($expires < time()) {
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->delete($key);
			return FALSE;
		}

		$len = (integer)fgets($fp, 20);
		$data = fread($fp, $len);

		flock($fp, LOCK_UN);
		fclose($fp);

		$data = unserialize($data);
		return $data;
	}

	/**
	 * Deletes a cache entry
	 * 
	 * @param string $key  The cache key
	 * @return boolean
	 */
	public function delete($key) {
		return @unlink($this->_config['path'] . '/cache_' . sha1($key) . '.txt');
	}
}

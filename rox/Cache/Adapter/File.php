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
 * Rox_Cache_Adapter_File
 *
 * @package Rox
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Cache_Adapter_File extends Rox_Cache_Adapter_Abstract {

	/**
	 * Path where to save cache files
	 *
	 * @var string
	 */
	protected $_cacheDir;

	/**
	 * Class constructor
	 *
	 * @param array $options
	 */
	public function __construct($options) {
		if (isset($options['cache_dir'])) {
			$this->_setCacheDir($options['cache_dir']);
		} else {
			$this->_setCacheDir(APP.'tmp'.DS.'cache'.DS);
		}
	}

	/**
	 * Cache_Adapter_File::setCacheDir()
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param mixed $expires
	 */
	protected function _setCacheDir($cacheDir) {
		if (!is_dir($cacheDir)) {
			throw new Exception('Cache directory does not exists');
		}

		$this->_cacheDir = $cacheDir;
	}

	/**
	 * Cache_Adapter_File::write()
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param mixed $expires
	 */
	public function write($key, &$data, $expires) {
		if (is_string($expires)) {
			$expires = strtotime($expires);
		} else {
			$expires = time()+$expires;
		}

		$serializedData = serialize($data);

		$fp = fopen($this->_cacheDir . 'cache_' . sha1($key) . '.txt', 'w');
		flock($fp, LOCK_EX);
		fwrite($fp, $expires . "\n");
		fwrite($fp, strlen($serializedData) . "\n");
		fwrite($fp, $serializedData);
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	/**
	 * Cache_Adapter_File::read()
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function read($key) {
		$fp = @fopen($this->_cacheDir . 'cache_' . sha1($key) . '.txt', 'r');
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
	 * Cache_Adapter_File::delete()
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		return @unlink($this->_cacheDir . 'cache_' . sha1($key) . '.txt');
	}
}

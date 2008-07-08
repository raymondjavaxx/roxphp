<?php
/**
 * Cache_Adapter_File
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Cache_Adapter_File extends Cache_Adapter_Abstract {

	/**
	 * Path where to save cache files
	 *
	 * @var string
	 */
	protected $cacheDir;

	/**
	 * Class constructor
	 *
	 * @param array $options
	 */
	public function __construct($options) {
		if (isset($options['cache_dir'])) {
			$this->setCacheDir($options['cache_dir']);
		} else {
			$this->setCacheDir(APP . 'tmp' . DS . 'cache' . DS);
		}
	}

	/**
	 * Cache_Adapter_File::setCacheDir()
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param mixed $expires
	 */
	protected function setCacheDir($cacheDir) {
		if (!is_dir($cacheDir)) {
			throw new Exception('Cache directory does not exists');
		}

		$this->cacheDir = $cacheDir;
	}

	/**
	 * Cache_Adapter_File::getCacheDir()
	 *
	 * @return string
	 */
	protected function getCacheDir() {
		return $this->cacheDir;
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

		$fp = fopen($this->getCacheDir() . 'cache_' . sha1($key) . '.txt', 'w');
		flock($fp, LOCK_EX);
		fwrite($fp, $expires . "\n");
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
		$fp = @fopen($this->getCacheDir() . 'cache_' . sha1($key) . '.txt', 'r');
		if ($fp === false) {
			return false;
		}

		flock($fp, LOCK_EX);
		$expires = fgets($fp, 20);
		settype($expires, 'integer');
		if ($expires < time()) {
			flock($fp, LOCK_UN);
			fclose($fp);
			$this->delete($key);
			return FALSE;
		}

		$data = fread($fp, 8000);

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
		return @unlink($this->getCacheDir() . 'cache_' . sha1($key) . '.txt');
	}
}
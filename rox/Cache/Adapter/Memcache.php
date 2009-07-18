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
 * Rox_Cache_Adapter_Memcache
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
	protected $Memcache;

	/**
	 * Constructor
	 *
	 * @param mixed $options
	 */
	public function __construct($options) {
		if (isset($options['servers'])) {
			$this->_servers = $options['servers'];
		}

		$this->Memcache = new Memcache;
		$this->_connect();
	}

	/**
	 * Rox_Cache_Adapter_Memcache::connect()
	 */
	protected function _connect() {
		foreach ($this->_servers as $host => $port) {
			$this->Memcache->addServer($host, $port);
		}
	}

	/**
	 * Rox_Cache_Adapter_Memcache::write()
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param mixed $expires
	 * @return boolean
	 */
	public function write($key, &$data, $expires) {
		if (is_string($expires)) {
			$expires = strtotime($expires);
		} else {
			$expires = time()+$expires;
		}

		return $this->Memcache->set($key, $data, 0, $expires);
	}

	/**
	 * Rox_Cache_Adapter_Memcache::read()
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function read($key) {
		return $this->Memcache->get($key);
	}

	/**
	 * Rox_Cache_Adapter_Memcache::delete()
	 *
	 * @param mixed $key
	 * @return boolean
	 */
	public function delete($key) {
		return $this->Memcache->delete($key);
	}
}
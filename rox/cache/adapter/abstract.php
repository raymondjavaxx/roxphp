<?php
/**
 * Cache_Adapter_Abstract
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
abstract class Cache_Adapter_Abstract {

	/**
	 * Cache_Adapter_Abstract::__construct()
	 * 
	 * @param array $options
	 * @return void
	 */
	abstract public function __construct($options);

	/**
	 * Cache_Adapter_Abstract::write()
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @param string|integer $expires
	 * @return boolean
	 */
	abstract public function write($key, &$data, $expires);

	/**
	 * Cache_Adapter_Abstract::read()
	 * 
	 * @param string $key
	 * @return mixed
	 */
	abstract public function read($key);

	/**
	 * Cache_Adapter_Abstract::delete()
	 * 
	 * @param string $key
	 * @return boolean
	 */
	abstract public function delete($key);
}
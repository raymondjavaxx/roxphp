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
 * Request
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Request {

	/**
	 * Request::getPost()
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getPost($key = null, $default = null) {
		if ($key === null) {
			return $_POST;
		}

		return isset($_POST[$key]) ? $_POST[$key] : $default;
	}

	/**
	 * Request::getQuery()
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getQuery($key = null, $default = null) {
		if ($key === null) {
			return $_GET;
		}

		return isset($_GET[$key]) ? $_GET[$key] : $default;
	}

	/**
	 * Request::getServer()
	 * 
	 * @param mixed $key
	 * @param mixed $default
	 * @return
	 */
	public function getServer($key = null, $default = null) {
		if ($key === null) {
			return $_SERVER;
		}

		return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;		
	}

	/**
	 * Request::getMethod()
	 * 
	 * @return string
	 */
	public function getMethod() {
		return $this->getServer('REQUEST_METHOD');
	}

	/**
	 * Return TRUE if the HTTP request method is POST
	 * 
	 * @return boolean
	 */
	public function isPost() {
		return $this->getMethod() == 'POST';
	}

	/**
	 * Return TRUE if the HTTP request method is GET
	 * 
	 * @return boolean
	 */
	public function isGet() {
		return $this->getMethod() == 'GET';
	}

	/**
	 * Return TRUE if the HTTP request method is PUT
	 *
	 * @return boolean
	 */
	public function isPut() {
		return $this->getMethod() == 'PUT';
	}

	/**
	 * Return TRUE if the HTTP request method is DELETE
	 *
	 * @return boolean
	 */
	public function isDelete() {
		return $this->getMethod() == 'DELETE';
	}

	/**
	 * Request::isAjax()
	 * 
	 * @return boolean
	 */
	public function isAjax() {
		return $this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
	}

	/**
	 * Request::isSSL()
	 * 
	 * @return boolean
	 */
	public function isSSL() {
		$ssl = $this->getServer('HTTPS');
		return $ssl === true || $ssl == 'on';
	}

	/**
	 * Detects iPhone/iPod touch
	 * 
	 * @return boolean
	 */
	public function isIPhone() {
		return preg_match('/iP[hone|od]/', $this->getServer('HTTP_USER_AGENT', ''));
	}

}

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

namespace rox\http;

/**
 * Request
 *
 * @package Rox
 */
class Request {

	/**
	 * Request data
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * Retrieves request data
	 *
	 * @param string $key 
	 * @param mixed $default 
	 * @return mixed
	 */
	public function data($key = null, $default = null) {
		if ($key === null) {
			return $this->data;
		}

		return isset($this->data[$key]) ? $this->data[$key] : $default;
	}

	/**
	 * Wrapper for <code>$_POST</code>
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
	 * Wrapper for <code>$_GET</code>
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
	 * Wrapper for <code>$_SERVER</code>
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getServer($key = null, $default = null) {
		if ($key === null) {
			return $_SERVER;
		}

		return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;		
	}

	/**
	 * Returns the HTTP request method
	 * 
	 * @return string
	 */
	public function getMethod() {
		return $this->getServer('REQUEST_METHOD');
	}

	/**
	 * Return true if the HTTP request method is POST
	 * 
	 * @return boolean
	 */
	public function isPost() {
		return $this->getMethod() == 'POST';
	}

	/**
	 * Return true if the HTTP request method is GET
	 * 
	 * @return boolean
	 */
	public function isGet() {
		return $this->getMethod() == 'GET';
	}

	/**
	 * Return true if the HTTP request method is PUT
	 *
	 * @return boolean
	 */
	public function isPut() {
		return $this->getMethod() == 'PUT';
	}

	/**
	 * Return true if the HTTP request method is DELETE
	 *
	 * @return boolean
	 */
	public function isDelete() {
		return $this->getMethod() == 'DELETE';
	}

	/**
	 * Returns true if the page was requested via AJAX
	 * 
	 * @return boolean
	 */
	public function isAjax() {
		return $this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
	}

	/**
	 * Returns true if the page was requested through SSL
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
	public function isIphone() {
		return preg_match('/iP[hone|od]/', $this->getServer('HTTP_USER_AGENT', '')) === 1;
	}

}

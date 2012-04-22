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

use \rox\http\request\ParamCollection;
use \rox\http\request\ServerParamCollection;

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

	public $server;

	public $headers;

	public function __construct() {
		$this->server = new ServerParamCollection($_SERVER);
		$this->headers = new ParamCollection($this->server->getHeaders());
	}

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
	public function getServer($key, $default = null) {
		return isset($this->server[$key]) ? $this->server[$key] : $default;
	}

	/**
	 * Returns the HTTP request method
	 * 
	 * @return string
	 */
	public function method() {
		return $this->getServer('REQUEST_METHOD');
	}

	/**
	 * Check if the request method matches a given HTTP method
	 *
	 * @param string $method 
	 * @return boolean
	 */
	public function is($method) {
		return (strcmp($this->method(), $method) === 0);
	}

	/**
	 * Return true if the HTTP request method is POST
	 * 
	 * @return boolean
	 */
	public function isPost() {
		return $this->is('POST');
	}

	/**
	 * Return true if the HTTP request method is GET
	 * 
	 * @return boolean
	 */
	public function isGet() {
		return $this->is('GET');
	}

	/**
	 * Return true if the HTTP request method is PUT
	 *
	 * @return boolean
	 */
	public function isPut() {
		return $this->is('PUT');
	}

	/**
	 * Return true if the HTTP request method is DELETE
	 *
	 * @return boolean
	 */
	public function isDelete() {
		return $this->is('DELETE');
	}

	/**
	 * Returns true if the page was requested via AJAX
	 * 
	 * @return boolean
	 */
	public function isAjax() {
		return $this->headers['x-requested-with'] === 'XMLHttpRequest';
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
}

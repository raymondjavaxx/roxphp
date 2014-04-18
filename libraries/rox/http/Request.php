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
use \rox\http\request\Normalizer;

/**
 * Request
 *
 * @package Rox
 */
class Request {

	/**
	 * Request params
	 *
	 * @var \rox\http\request\ParamCollection
	 */
	public $data;

	/**
	 * Server params
	 *
	 * @var \rox\http\request\ServerParamCollection
	 */
	public $server;

	/**
	 * HTTP request headers
	 *
	 * @var \rox\http\request\ParamCollection
	 */
	public $headers;

	/**
	 * HTTP request method
	 *
	 * @var string
	 */
	protected $_method;

	/**
	 * Constructor
	 *
	 * @param array $query 
	 * @param array $data 
	 * @param array $server 
	 */
	public function __construct($query = array(), $data = array(), $server = array()) {
		$this->query   = new ServerParamCollection($query);
		$this->data    = new ServerParamCollection($data);
		$this->server  = new ServerParamCollection($server);
		$this->headers = new ParamCollection($this->server->getHeaders());
	}

	/**
	 * Creates a new request object
	 *
	 * @return \rox\http\Request
	 */
	public static function fromGlobals() {
		$request = new static($_GET, $_POST, $_SERVER);
		Normalizer::normalize($request);
		return $request;
	}

	/**
	 * Returns the HTTP request method
	 * 
	 * @return string
	 */
	public function method() {
		if ($this->_method === null) {
			$this->_method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
			if ($this->_method === 'POST') {
				$this->_method = strtoupper($this->data->get('_method', 'POST'));
			}
		}

		return $this->_method;
	}

	/**
	 * Returns the raw request body
	 *
	 * @return string
	 */
	public function rawBody() {
		return file_get_contents('php://input');
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
		if ($this->headers['x-forwarded-proto'] === 'https') {
			// SSL terminated by load balancer/reverse proxy
			return true;
		}

		$ssl = $this->server->get('HTTPS');
		return $ssl === true || $ssl === 'on';
	}

	// ------------------------------------------------
	//  Deprecated
	// -----------------------------------------------

	/**
	 * Retrieves request data
	 *
	 * @param string $key 
	 * @param mixed $default 
	 * @return mixed
	 * @deprecated
	 */
	public function data($key = null, $default = null) {
		if ($key === null) {
			return $this->data->all();
		}

		return $this->data->get($key, $default);
	}

	/**
	 * Wrapper for <code>$_POST</code>
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 * @deprecated
	 */
	public function getPost($key = null, $default = null) {
		return $this->data($key, $default);
	}

	/**
	 * Wrapper for <code>$_GET</code>
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 * @deprecated
	 */
	public function getQuery($key, $default = null) {
		return $this->query->get($key, $default);
	}

	/**
	 * Wrapper for <code>$_SERVER</code>
	 * 
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 * @deprecated
	 */
	public function getServer($key, $default = null) {
		return $this->server->get($key, $default);
	}
}

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

use \rox\Exception;

/**
 * HTTP Response class
 *
 * @package Rox
 */
class Response {

	/**
	 * Protocol version
	 *
	 * @var string
	 */
	public $protocol = 'HTTP/1.1';

	/**
	 * HTTP status
	 *
	 * @var string
	 */
	public $status = 200;

	/**
	 * Http headers
	 *
	 * @var string
	 */
	public $headers = array();

	/**
	 * Response body
	 *
	 * @var string
	 */
	public $body;

	protected $_statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	public function header($name, $value) {
		$this->headers[$name] = $value;
	}

	public function render() {
		header($this->_statusHeader(), true);

		foreach ($this->headers as $key => $value) {
			$header = "{$key}: {$value}";
			header($header, true);
		}

		echo $this->body;
	}

	protected function _statusHeader() {
		if (!isset($this->_statuses[$this->status])) {
			throw new Exception('Invalid status code');
		}

		return "{$this->protocol} {$this->status} {$this->_statuses[$this->status]}";
	}
}

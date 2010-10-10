<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * SMTP Mailer
 *
 * @package Rox
 */
class Rox_Mailer_Smtp extends Rox_Mailer_Abstract {

	/**
	 * Connection resource
	 *
	 * @var resource
	 */
	protected $_fp;

	/**
	 * SMTP options
	 *
	 * @var array
	 */
	protected $_options = array(
		'host'     => '127.0.0.1',
		'username' => null,
		'password' => null,
		'port'     => 25,
		'timeout'  => 4
	);

	/**
	 * Sends email message
	 *
	 * @param Rox_Mailer_Message
	 * @return mixed
	 */
	public function send(Rox_Mailer_Message $message) {
		$this->_connect();

		$this->_sendLine('EHLO roxphp', 250);
		$this->_sendLine('AUTH LOGIN', 334);
		$this->_sendLine(base64_encode($this->_options['username']), 334);
		$this->_sendLine(base64_encode($this->_options['password']), 235);
		$this->_sendLine('MAIL FROM:<' . $message->from . '>', 250);

		$allRecipients = array_merge((array)$message->to, (array)$message->cc, (array)$message->bcc);
		foreach ($allRecipients as $recipient) {
			$this->_sendLine('RCPT TO:<' . $recipient . '>', 250);
		}

		$this->_sendLine('DATA', 354);
		$this->_sendData($email->serialize());
		$this->_sendData("\r\n\r\n\r\n.\r\n", 250);
		$this->_sendLine('QUIT');

		$this->_disconnect();
	}

	/**
	 * Connects to SMTP server
	 * 
	 * @return void
	 * @throws Mailer_Adapter_Rox_Exception
	 */
	protected function _connect() {
		$this->_fp = @fsockopen($this->_options['host'], $this->_options['port'],
			$errno, $errstr, $this->_options['timeout']);

		if ($this->_fp === false) {
			throw new Rox_Exception('Could not connect to host ' . $this->_options['host']);
		}

		// consume the response
		$this->_getResponse();
	}

	/**
	 * Rox_Mailer_Smtp::_sendData()
	 * 
	 * @param mixed $data
	 * @param mixed $expectedCode
	 * @return void
	 * @throws Mailer_Adapter_Exception
	 */
	protected function _sendData($data, $expectedCode = null) {
		fputs($this->_fp, $data);

		if (!is_null($expectedCode)) {
			$response = $this->_getResponse();
			if (strpos($response, (string)$expectedCode) === false) {
				throw new Rox_Exception("Unexpected response '{$response}'");
			}
		}
	}

	/**
	 * Rox_Mailer_Smtp::_sendLine()
	 * 
	 * @param mixed $data
	 * @param mixed $expectedCode
	 * @return void
	 */
	protected function _sendLine($data, $expectedCode = null) {
		$this->_sendData($data . "\r\n", $expectedCode);
	}

	/**
	 * Rox_Mailer_Smtp::_getResponse()
	 * 
	 * @return string|false
	 */
	protected function _getResponse() {
		do {
			$data = @fgets($this->_fp);
			$status = stream_get_meta_data($this->_fp);
		} while ($status['unread_bytes'] > 0);

		return $data;
	}

	/**
	 * Rox_Mailer_Smtp::_disconnect()
	 * 
	 * @return boolean
	 */
	protected function _disconnect() {
		if (is_null($this->_fp)) {
			return false;
		}

		return fclose($this->_fp);
	}
}

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
class Rox_Mailer_Adapter_Smtp extends Rox_Mailer_Adapter {

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
	protected $_config = array(
		'host'     => '127.0.0.1',
		'username' => null,
		'password' => null,
		'port'     => 25,
		'timeout'  => 4
	);

	/**
	 * Sends email message
	 *
	 * @param Rox_Mailer_Message $message
	 * @return void
	 */
	public function send(Rox_Mailer_Message $message) {
		$this->_connect();

		$this->_sendLine('EHLO roxphp', 250);

		if (!empty($this->_config['username'])) {
			$this->_sendLine('AUTH LOGIN', 334);
			$this->_sendLine(base64_encode($this->_config['username']), 334);
			$this->_sendLine(base64_encode($this->_config['password']), 235);
		}

		$this->_sendLine('MAIL FROM:<' . $message->from . '>', 250);

		$allRecipients = array_merge((array)$message->to, (array)$message->cc, (array)$message->bcc);
		foreach ($allRecipients as $recipient) {
			$this->_sendLine('RCPT TO:<' . $recipient . '>', 250);
		}

		$this->_sendLine('DATA', 354);
		$this->_sendData($message->serialize());
		$this->_sendData("\r\n.\r\n", 250);
		$this->_sendLine('QUIT');

		$this->_disconnect();
	}

	/**
	 * Connects to SMTP server
	 * 
	 * @return void
	 * @throws Rox_Exception
	 */
	protected function _connect() {
		$this->_fp = @fsockopen($this->_config['host'], $this->_config['port'],
			$errno, $errstr, $this->_config['timeout']);

		if ($this->_fp === false) {
			throw new Rox_Exception('Could not connect to host ' . $this->_config['host']);
		}

		// consume the response
		$this->_getResponse();
	}

	/**
	 * Sends data to SMTP server
	 * 
	 * @param mixed $data
	 * @param mixed $expectedCode
	 * @return void
	 * @throws Rox_Exception
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
	 * Sends a line of text to the server
	 * 
	 * @param string $text
	 * @param integer $expectedCode
	 * @return void
	 */
	protected function _sendLine($text, $expectedCode = null) {
		$this->_sendData($text . "\r\n", $expectedCode);
	}

	/**
	 * Get response from server
	 * 
	 * @return string
	 */
	protected function _getResponse() {
		do {
			$result = trim(fgets($this->_fp, 1024));
		} while (strpos($result, '-') === 3);

		return $result;
	}

	/**
	 * Disconnects from SMTP server
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

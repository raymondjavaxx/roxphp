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
 * @subpackage Rox_Mailer
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */
 
/**
 * @see Rox_Mailer_Abstract
 */
require_once 'Abstract.php';

/**
 * SMTP Mailer
 *
 * @package Rox
 * @subpackage Rox_Mailer
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
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
		'host'     => null,
		'username' => null,
		'password' => null,
		'port'     => 25,
		'timeout'  => 4
	);

	/**
	 * Rox_Mailer_Smtp::send()
	 * 
	 * @return void
	 */
	public function send() {
		$this->_connect();
		$this->_sendLine('EHLO roxphp');
		$this->_sendLine('AUTH LOGIN', 250);
		$this->_sendLine(base64_encode($this->_options['username']), 334);
		$this->_sendLine(base64_encode($this->_options['password']), 334);
		$this->_sendLine('MAIL FROM:<' . $this->_from . '>', 250);

		$allRecipients = array_merge($this->_to, $this->_cc, $this->_bcc);
		foreach ($allRecipients as $recipient) {
			$this->_sendLine('RCPT TO:<' . $recipient . '>', 250);
		}

		$this->_sendLine('DATA', 354);

		$this->_sendEmailHeader();
		$this->_sendData($this->_message);
		$this->_sendData("\r\n\r\n\r\n.\r\n", 250);

		$this->_sendLine('QUIT');

		$this->_disconnect();
	}

	/**
	 * Rox_Mailer_Smtp::_sendEmailHeader()
	 * 
	 * @return void
	 */
	protected function _sendEmailHeader() {
		$this->_sendLine('From: ' . $this->_from);

		$this->_sendLine('To: ' . implode(', ', $this->_to));
		$this->_sendLine('Cc: ' . implode(', ', $this->_cc));
		$this->_sendLine('Bcc: ' . implode(', ', $this->_bcc));
		$this->_sendLine('X-Mailer: RoxPHP SMTP Mailer');

		$this->_sendLine('Subject:' . $this->_subject);
		$this->_sendLine('Content-Type: text/plain; charset=UTF-8');
	}

	/**
	 * Connects to SMTP server
	 * 
	 * @return void
	 * @throws Mailer_Adapter_Exception
	 */
	protected function _connect() {
		$this->_fp = @fsockopen($this->_options['host'], $this->_options['port'],
			$errno, $errstr, $this->_options['timeout']);

		if ($this->_fp === false) {
			throw new Exception('Could not connect to host ' . $this->_options['host']);
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
				//throw new Exception('Expected response code ' . $expectedCode . ' not found');
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

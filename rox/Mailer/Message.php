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
 * Mailer Message
 *
 * @package Rox
 */
class Rox_Mailer_Message {

	protected $_headers = array();

	protected $_parts = array();

	/**
	 * Sender email address
	 * 
	 * @var string
	 */
	public $from = '';

	/**
	 * Email recipients
	 * 
	 * @var array
	 */
	public $to = array();

	/**
	 * CC recipients
	 * 
	 * @var array
	 */
	public $cc = array();

	/**
	 * BCC recipients
	 * 
	 * @var array
	 */
	public $bcc = array();

	/**
	 * Email subject
	 * 
	 * @var string
	 */
	public $subject = '';

	public function __construct($defaults = array()) {
		foreach ($defaults as $k => $v) {
			$this->{$k} = $v;
		}
	}

	public function setHeader($name, $value = null) {
		if (is_array($name)) {
			$this->_headers += $name;
			return;
		}

		$this->_headers[$name] = $value;
	}

	public function addPart($contentType, $data) {
		$this->_parts[] = array('content_type' => $contentType, 'data' => $data);
	}

	public function serialize() {
		$lines = array();

		$lines[] = 'From: ' . $this->from;
		$lines[] = 'To: ' . implode(', ', (array)$this->to);
		$lines[] = 'Cc: ' . implode(', ', (array)$this->cc);
		$lines[] = 'Bcc: ' . implode(', ', (array)$this->bcc);
		$lines[] = 'X-Mailer: RoxPHP SMTP Mailer';

		foreach ($this->_headers as $name => $value) {
			$lines[] = "{$name}: {$value}";
		}

		$lines[] = 'Subject:' . $this->subject;

		if (count($this->_parts)) {
			$lines[] = $this->serializeParts();
		}

		return implode("\r\n", $lines);
	}

	public function serializeParts() {
		$boundary = uniqid('roxbound', true);

		$lines = array();
		$lines[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
		$lines[] = '';

		foreach ($this->_parts as $part) {
			$lines[] = '--' . $boundary;
			$lines[] = 'Content-Type: ' . $part['content_type'];
			$lines[] = '';
			$lines[] = $part['data'];
		}

		$lines[] = '--' . $boundary;

		return implode("\r\n", $lines);
	}
}

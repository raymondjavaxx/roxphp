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
		$this->set($defaults);
	}

	public function set($attributes) {
		foreach ($attributes as $k => $v) {
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

	public function addPart($contentType, $data, $headers = array()) {
		$this->_parts[] = array(
			'content_type' => $contentType,
			'data' => $data,
			'headers' => $headers
		);
	}

	public function addAttachment($filename, $data) {
		$encodedData = wordwrap(base64_encode($data), 76, "\n", true);
		$this->addPart('application/octet-stream', $encodedData, array(
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
			'Content-Transfer-Encoding' => 'base64'
		));
	}

	public function addQuotedPrintablePart($contentType, $data, $headers = array()) {
		$this->addPart($contentType, self::_encodeText($data), array(
			'Content-Transfer-Encoding' => 'quoted-printable'
		));
	}

	/**
	 * Serializes the object to MIME
	 *
	 * @param array $options 
	 * @return string
	 */
	public function serialize($options = array()) {
		$lines = array();

		$lines[] = 'MIME-Version: 1.0';
		$lines[] = 'From: ' . $this->from;
		$lines[] = 'To: ' . implode(', ', (array)$this->to);

		if (!empty($this->cc)) {
			$lines[] = 'Cc: ' . implode(', ', (array)$this->cc);
		}

		if (!empty($this->bcc)) {
			$lines[] = 'Bcc: ' . implode(', ', (array)$this->bcc);
		}

		$lines[] = 'Subject: ' . $this->subject;
		$lines[] = 'X-Mailer: RoxPHP Mailer';

		foreach ($this->_headers as $name => $value) {
			$lines[] = "{$name}: {$value}";
		}

		if (count($this->_parts)) {
			$lines[] = $this->serializeParts($options);
		}

		return implode("\n", $lines);
	}

	public function serializeParts($options = array()) {
		if (!isset($options['boundary'])) {
			$options['boundary'] = uniqid('rox', true);
		}

		$lines = array();
		$lines[] = 'Content-Type: multipart/alternative; boundary="' . $options['boundary'] . '"';
		$lines[] = '';
		$lines[] = 'This is a multi-part message in MIME format';
		$lines[] = '';

		foreach ($this->_parts as $part) {
			$lines[] = "--{$options['boundary']}";
			$lines[] = 'Content-Type: ' . $part['content_type'];

			foreach ($part['headers'] as $name => $value) {
				$lines[] = "{$name}: {$value}";
			}

			$lines[] = '';
			$lines[] = $part['data'];
		}

		$lines[] = "--{$options['boundary']}--";

		return implode("\n", $lines);
	}

	/**
	 * Encodes text to Quoted-printable
	 *
	 * @param string $text 
	 * @return string
	 */
	protected static function _encodeText($text) {
		$chars  = str_split($text);
		foreach ($chars as &$char) {
			if ($char == '=' || ord($char) > 127) {
				$char = '=' . strtoupper(bin2hex($char));
			}
		}

		$lines = explode("\n", implode('', $chars));
		foreach ($lines as &$line) {
			$line = wordwrap($line, 70, "=\n");
		}

		return implode("\n", $lines);
	}
}

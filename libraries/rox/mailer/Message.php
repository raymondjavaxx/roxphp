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

namespace rox\mailer;

/**
 * Mailer Message
 *
 * @package Rox
 */
class Message {

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

	/**
	 * Sets email header
	 *
	 * @param string $name 
	 * @param string $value 
	 * @return void
	 */
	public function setHeader($name, $value = null) {
		if (is_array($name)) {
			$this->_headers += $name;
			return;
		}

		$this->_headers[$name] = $value;
	}

	public function fromEmailAddress() {
		return $this->_extractEmailAddress($this->from);
	}

	protected function _formatEmailAddress($email) {
		if (is_array($email)) {
			if (isset($email['name']) && isset($email['email'])) {
				return self::_encodeTextInline($email['name']) . ' <' . $email['email'] . '>';
			} else {
				throw new Exception();
			}
		}

		return (string)$email;
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
		$encodedData = explode("\n", quoted_printable_encode($data));

		foreach ($encodedData as &$line) {
			if (strpos($line, '.') === 0) {
				$line = '.' . $line;
			}
		}

		$this->addPart($contentType, implode("\n", $encodedData), array(
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
		$lines[] = 'From: ' . $this->_formatEmailAddress($this->from);
		$lines[] = 'To: ' . implode(', ', (array)$this->to);

		if (!empty($this->cc)) {
			$lines[] = 'Cc: ' . implode(', ', (array)$this->cc);
		}

		if (!empty($this->bcc)) {
			$lines[] = 'Bcc: ' . implode(', ', (array)$this->bcc);
		}

		$lines[] = 'Subject: ' . self::_encodeTextInline($this->subject);
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

	protected function _extractEmailAddress($email) {
		if (is_array($email) && isset($email['email'])) {
			return $email['email'];
		}

		return (string)$email;
	}

	protected static function _encodeTextInline($text) {
		$encoded = quoted_printable_encode($text);
		return $encoded == $text ? $text : '=?UTF-8?Q?' . $encoded . '?=';
	}
}

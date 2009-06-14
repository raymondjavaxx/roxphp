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
 * Base class for mailer
 *
 * @package Rox
 * @subpackage Rox_Mailer
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
abstract class Rox_Mailer_Abstract {

	/**
	 * Mailer options
	 *
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Sender email address
	 * 
	 * @var string
	 */
	protected $_from = '';

	/**
	 * Email recipients
	 * 
	 * @var array
	 */
	protected $_to = array();

	/**
	 * CC recipients
	 * 
	 * @var array
	 */
	protected $_cc = array();

	/**
	 * BCC recipients
	 * 
	 * @var array
	 */
	protected $_bcc = array();

	/**
	 * Email subject
	 * 
	 * @var string
	 */
	protected $_subject = '';

	/**
	 * Email message
	 * 
	 * @var string
	 */
	protected $_message = '';

	/**
	 * Constructor
	 * 
	 * @param mixed $options
	 * @return void
	 */
	public function __construct(array $options = null) {
		if ($options !== null) {
			$this->_options = array_merge($this->_options, $options);			
		}
	}

	/**
	 * Sends the email
	 * 
	 * @return mixed
	 */
	abstract public function send();

	// ----------------------------------------------------
	//  Setters and Getters
	// ----------------------------------------------------

	/**
	 * Sets the message body
	 * 
	 * @param mixed $message
	 * @return void
	 */
	public function setMessage($message) {
		$this->_message = $message;
	}

	/**
	 * Sets the subject
	 * 
	 * @param mixed $subject
	 * @return void
	 */
	public function setSubject($subject) {
		$this->_subject = $subject;
	}

	/**
	 * Sets the sender email
	 * 
	 * @param mixed $from
	 * @return void
	 */
	public function setFrom($from) {
		$this->_from = $from;
	}

	/**
	 * Adds one or more recipients
	 * 
	 * @param string|array $to
	 * @return void
	 */
	public function addTo($to) {
		if (!is_array($to)) {
			$to = array($to);
		}

		$this->_to = array_merge($this->_to, $to);
	}

	/**
	 * Adds one or more recipients of CC
	 * 
	 * @param string|array $cc
	 * @return void
	 */
	public function addCc($cc) {
		if (!is_array($cc)) {
			$cc = array($cc);
		}

		$this->_cc = array_merge($this->_cc, $cc);
	}

	/**
	 * Adds one or more recipients of BCC
	 * 
	 * @param string|array $bcc
	 * @return void
	 */
	public function addBcc($bcc) {
		if (!is_array($bcc)) {
			$bcc = array($bcc);
		}

		$this->_bcc = array_merge($this->_bcc, $bcc);
	}
}

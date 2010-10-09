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
 * Base class for mailer
 *
 * @package Rox
 */
abstract class Rox_Mailer_Abstract {

	/**
	 * Mailer options
	 *
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Constructor
	 * 
	 * @param mixed $options
	 * @return void
	 */
	public function __construct($options = array()) {
		$this->_options += $options;
	}

	/**
	 * Sends email message
	 *
	 * @param Rox_Mailer_Message
	 * @return mixed
	 */
	abstract public function send(Rox_Mailer_Message $message);
}

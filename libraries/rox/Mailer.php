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

namespace rox;

use \rox\mailer\Message;
use \rox\Inflector;

/**
 * Mailer
 *
 * @package Rox
 */
class Mailer {

	const ADAPTER_SMTP = '\rox\mailer\adapter\Smtp';

	protected static $_config = array(
		'adapter' => self::ADAPTER_SMTP
	);

	public $defaults = array();

	public $message;

	public $params;

	/**
	 * Template variables
	 *
	 * @var array  
	 */
	protected $_templateVars = array();

	public function __construct() {
		$this->message = new Message($this->defaults);
		$this->message->setHeader(array('Date' => date(DATE_RFC2822)));
	}

	public static function init($config = array()) {
		self::$_config = ($config + self::$_config);
	}

	/**
	 * Sets a template variable
	 *
	 * @param string|array $varName
	 * @param mixed $value
	 */
	public function set($varName, $value = null) {
		if (is_array($varName)) {
			$this->_templateVars += $varName;
		} else {
			$this->_templateVars[$varName] = $value;
		}
	}

	/**
	 * Sends the email
	 * 
	 * @return mixed
	 */
	private function _send() {
		$folder   = $this->params['mailer'];
		$filename = $this->params['email'];

		$extensions = array(
			'txt.tpl' => 'text/plain; charset="utf-8"',
			'html.tpl' => 'text/html; charset="utf-8"'
		);

		foreach ($extensions as $extension => $contentType) {
			$path = ROX_APP_PATH . "/mailers/templates/{$folder}/{$filename}.{$extension}";
			if (file_exists($path)) {
				$this->message->addQuotedPrintablePart($contentType, self::_renderTemplate($path));
			}
		}

		$adapter = new self::$_config['adapter'](self::$_config);
		return $adapter->send($this->message);
	}

	/**
	 * Renders the email template
	 * 
	 * @param string $templatePath
	 * @return string
	 */
	private function _renderTemplate($templatePath) {
		extract($this->_templateVars, EXTR_SKIP);
		ob_start();
		require $templatePath;
		return ob_get_clean();
	}

	/**
	 * Mailer::send()
	 * 
	 * @param string $mailerAndEmail
	 * @param ...
	 * @return mixed
	 */
	public static function send($mailerAndEmail) {
		if (strpos($mailerAndEmail, '.') == false) {
			throw new Exception('mailer and email should be separated by a period.');
		}

		list($mailer, $email) = explode('.', $mailerAndEmail);
		$mailerClass = Inflector::camelize($mailer . '_mailer');
		$emailMethod = Inflector::lowerCamelize($email);
		$args = array_slice(func_get_args(), 1);

		$mailerInstance = new $mailerClass;
		$mailerInstance->params = array(
			'mailer'       => $mailer,
			'mailer_class' => $mailerClass,
			'email'        => $email,
			'email_method' => $emailMethod
		);

		call_user_func_array(array($mailerInstance, $emailMethod), $args);
		return $mailerInstance->_send();
	}
}

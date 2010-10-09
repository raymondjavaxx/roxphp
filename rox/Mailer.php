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
 * Rox_Mailer
 *
 * @package Rox
 */
class Rox_Mailer {

	public $recipients;
	public $from;
	public $subject;
	public $body = array();
	public $cc = array();
	public $bcc = array();
	public $replyTo;

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected static $_settings = array(
		'adapter' => 'smtp',
		'adapter_settigns' => array() 
	);

	/**
	 * Sets the adapter and settings for the mailer module.
	 * 
	 * @param string $adapter
	 * @param array $settings
	 */
	public static function setAdapter($adapter, $settings = array()) {
		self::$_settings = array('adapter' => $adapter, 'adapter_settings' => $settings);
	}

	/**
	 * Sends the email.
	 * 
	 * @return boolean
	 */
	private function _send($mailer, $method) {
		$adapterClassName = 'Rox_Mailer_' . Rox_Inflector::camelize(self::$_settings['adapter']);
		$adapter = new $adapterClassName(self::$_settings['adapter_settings']);

		if (is_string($this->recipients)) {
			$this->recipients = array($this->recipients);
		}

		$adapter->addTo($this->recipients);
		$adapter->addBcc($this->bcc);
		$adapter->addCc($this->cc);

		$adapter->setFrom($this->from);
		$adapter->setSubject($this->subject);

		if (is_string($this->body)) {
			$adapter->setContentType('text/plain; charset=UTF-8');
			$adapter->setMessage($this->body);
			return $adapter->send();
		}

		$filename = Rox_Inflector::underscore($method);
		$folder = str_replace('_mailer', '', Rox_Inflector::underscore($mailer));
		$extensions = array('txt.tpl' => 'text/plain; charset=UTF-8', 'html.tpl' => 'text/html; charset=UTF-8');
		foreach ($extensions as $extension => $contentType) {
			$path = ROX_APP_PATH . "/mailers/templates/{$folder}/{$filename}.{$extension}";
			if (file_exists($path)) {
				$adapter->setContentType($contentType);
				$adapter->setMessage(self::_renderTemplate($path, $this->body));
				return $adapter->send();
			}
		}

		throw new Exception('No template found');
	}

	/**
	 * Renders the email template
	 * 
	 * @param string $templatePath
	 * @param array $vars template variables
	 * @return string
	 */
	private function _renderTemplate($templatePath, $vars) {
		extract($vars, EXTR_SKIP);
		ob_start();
		include $templatePath;
		return ob_get_clean();
	}

	/**
	 * Rox_Mailer::send()
	 * 
	 * @param string $mailerAndMethod
	 * @return boolean
	 */
	public static function send($mailerAndMethod) {
		if (strpos($mailerAndMethod, '.') == false) {
			throw new Rox_Exception('mailer and method should be separated by a period.');
		}

		list($mailerClass, $mailerMethod) = explode('.', $mailerAndMethod);
		$mailerClass = Rox_Inflector::camelize($mailerClass . '_mailer');
		$mailerMehod = Rox_Inflector::lowerCamelize($mailerMethod);
		$parameters = func_get_args();

		$mailer = new $mailerClass;
		call_user_func_array(array($mailer, $mailerMehod), array_slice($parameters, 1));

		return $mailer->_send($mailerClass, $mailerMehod);
	}
}

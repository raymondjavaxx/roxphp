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
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Rox_Mailer
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Mailer {

	public $recipients;
	public $from;
	public $subject;
	public $body;
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
		self::$_settings['adapter'] = $adapter;
		self::$_settings['adapter_settings'] = $settings;
	}

	/**
	 * Sends the email.
	 * 
	 * @return boolean
	 */
	private function _send($mailer, $method) {
		$adapterClassName = 'Rox_Mailer_' . Rox_Inflector::camelize(self::$_settings['adapter']);
		if (!class_exists($adapterClassName)) {
			$adapterFile = Rox_Inflector::camelize(self::$_settings['adapter']);
			require_once ROX . 'Mailer/' . $adapterFile . '.php'; 
		}

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
			$adapter->setMessage($this->body);
		} else {
			$folder   = str_replace('_mailer', '',Rox_Inflector::underscore($mailer));
			$filename = Rox_Inflector::underscore($method).'.phtml';
			$path     = ROX_APP_PATH.'/mailers/templates/'.$folder.'/'.$filename;

			$body = self::_renderTemplate($path, $this->body);
			$adapter->setMessage($body);
		}
		
		return $adapter->send();
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
			throw new Exception('mailer and method should be separated by a period.');
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

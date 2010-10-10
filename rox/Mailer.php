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

	public $defaults = array();

	/**
	 * undocumented variable
	 *
	 * @var Rox_Mailer_Message
	 */
	public $message;

	/**
	 * View variables
	 *
	 * @var array  
	 */
	protected $_templateVars = array();

	/**
	 * Settings
	 *
	 * @var array
	 */
	protected static $_settings = array(
		'adapter' => 'smtp',
		'adapter_settigns' => array() 
	);

	public function __construct() {
		$this->message = new Rox_Mailer_Message($this->defaults);
	}

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
	 * Sends the email.
	 * 
	 * @return boolean
	 */
	private function _send($method) {
		$adapterClassName = 'Rox_Mailer_Adapter_' . Rox_Inflector::camelize(self::$_settings['adapter']);
		$adapter = new $adapterClassName(self::$_settings['adapter_settings']);

		$folder = str_replace('_mailer', '', Rox_Inflector::underscore(get_class($this)));
		$filename = Rox_Inflector::underscore($method);
		$extensions = array('txt.tpl' => 'text/plain; charset=UTF-8', 'html.tpl' => 'text/html; charset=UTF-8');

		foreach ($extensions as $extension => $contentType) {
			$path = ROX_APP_PATH . "/mailers/templates/{$folder}/{$filename}.{$extension}";
			if (file_exists($path)) {
				$message->addPart($contentType, self::_renderTemplate($path));
			}
		}

		$adapter->send($message);
		//throw new Exception('No template found');
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

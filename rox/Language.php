<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Language
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Language {

	/**
	 * Locale instance
	 * 
	 * @var Locale
	 */
	private $_locale;

	/**
	 * Loaded translation tables
	 * 
	 * @var array
	 */
	private $_tables = array();

	/**
	 * Language::init()
	 * 
	 * @param Locale $locale
	 * @throws Exception
	 */
	public function init($locale = null) {
		if (is_null($locale)) {
			$locale = new Rox_Locale;
		} else if (!($locale instanceof Rox_Locale)) {
			throw new Exception('Param must be instance of Locale');
		}

		$this->_locale = $locale;
	}

	/**
	 * Language::getInstance()
	 *
	 * @return Language
	 */
	public static function getInstance() {
		static $instance;
		if (!is_object($instance)) {
			$instance = new Rox_Language;
		}

		return $instance;
	}

	/**
	 * Language::translateText()
	 * 
	 * @param mixed $text
	 * @param string $table
	 * @return string
	 */
	public function translateText($text, $table = 'messages') {
		if (!isset($this->_tables[$table][$text])) {
			return $text;
		}

		return $this->_tables[$table][$text];
	}

	/**
	 * Language::_()
	 * 
	 * @param mixed $text
	 * @param string $table
	 * @return string
	 */
	public static function _($text, $table = 'messages') {
		return self::getInstance()->translateText($text, $table);
	}

	/**
	 * Language::loadTable()
	 *
	 * @return void
	 */
	public function loadTable($name = 'messages') {
		$fp = @fopen(ROX_APP_PATH . DS . 'locale' . DS . $this->_locale->getTag() . DS . $name . '.csv', 'r');
		if ($fp === false) {
			throw new Exception('Can`t open locale file');
		}

		while (($data = fgetcsv($fp)) !== false) {
			$this->_tables[$name][$data[0]] = $data[1];
		}

		fclose($fp);
	}
}

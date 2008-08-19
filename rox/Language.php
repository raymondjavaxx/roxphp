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
class Language {

	/**
	 * Locale instance
	 * 
	 * @var Locale
	 */
	protected $locale;

	/**
	 * Loaded translation tables
	 * 
	 * @var array
	 */
	protected $tables = array();

	/**
	 * Language::init()
	 * 
	 * @param Locale $locale
	 */
	public function init($locale = null) {
		if (is_null($locale)) {
			$locale = new Locale;
		} else if (!($locale instanceof Locale)) {
			throw new Exception('Param must be instance of Locale');
		}

		$this->locale = $locale;
	}

	/**
	 * Language::getInstance()
	 *
	 * @return Language
	 */
	public static function getInstance() {
		static $instance;
		if (!is_object($instance)) {
			$instance = new Language;
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
		if (!isset($this->tables[$table][$text])) {
			return $text;
		}

		return $this->tables[$table][$text];
	}

	/**
	 * Language::_()
	 * 
	 * @param mixed $text
	 * @param string $table
	 * @return string
	 */
	public static function _($text, $table = 'messages') {
		return Language::getInstance()->translateText($text, $table);
	}

	/**
	 * Language::loadTable()
	 *
	 * @return void
	 */
	public function loadTable($name = 'messages') {
		$fp = @fopen(APP . 'locale' . DS . $this->locale->getTag() . DS . $name . '.csv', 'r');
		if ($fp === false) {
			throw new Exception('Can`t open locale file');
		}

		while (($data = fgetcsv($fp)) !== false) {
			$this->tables[$name][$data[0]] = $data[1];
		}

		fclose($fp);
	}
}

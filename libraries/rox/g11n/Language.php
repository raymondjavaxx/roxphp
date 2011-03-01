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

namespace rox\g11n;

use \rox\Exception;

/**
 * Language
 *
 * @package Rox
 */
class Language {

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
			$locale = new Locale;
		} else if (!($locale instanceof Locale)) {
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
	    $path = ROX_APP_PATH . "/locale/" . $this->_locale->getTag() . "/{$name}.csv";
		$fp = @fopen($path, 'r');
		if ($fp === false) {
			throw new Exception("Can't open locale file");
		}

		while (($data = fgetcsv($fp)) !== false) {
			$this->_tables[$name][$data[0]] = $data[1];
		}

		fclose($fp);
	}
}

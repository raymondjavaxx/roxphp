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

/**
 * Locale
 *
 * @package Rox
 */
class Locale {

	/**
	 * Locale tag
	 * 
	 * @var string
	 */
	protected $_tag = 'en_US';

	public function __construct($tag = null) {
		if ($tag !== null) {
			$this->setTag($tag);
		}
	}

	/**
	 * Returns current locale tag
	 * 
	 * @return string
	 */
	public function getTag() {
		return $this->_tag;
	}

	/**
	 * Sets current locale tag
	 * 
	 * @param string $tag
	 */
	public function setTag($tag) {
		$this->_tag = $tag;
	}

	/**
	 * Autodetect the locale based on browser preferences
	 *
	 * @param array $availableLocales Available locales
	 * @param bool $fallback
	 * @return boolean
	 */
	public function autoDetect($availableLocales, $fallback = false) {
		$locales = self::_detectBrowserLocales();
		if (empty($locales)) {
			return false;
		}

		foreach ($locales as $locale) {
			if (in_array($locale, $availableLocales)) {	
				$this->_tag = $locale;
				return true;
			}
		}

		if ($fallback) {
			//TODO: find a more elegant way of doing this
			foreach ($locales as $locale) {
				$subTags = explode('_', $locale);
				foreach ($availableLocales as $a) {
					if (strpos($a, $subTags[0]) === 0) {
						$this->_tag = $a;
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Gets locale tag from browser
	 * 
	 * @return array
	 */
	protected static function _detectBrowserLocales() {
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			return array();
		}

		$locales = array();
		$tags = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach ($tags as $tag) {
			if (!preg_match('/;q=\d+\.\d+$/', $tag)) {
				$tag .= ';q=1.0';
			}

			$pattern = "/^(?<language>[a-z]{2}).*?(?<country>[a-z]{2})?;q=?(?<qvalue>\d+\.\d+)$/";
			if (preg_match($pattern, trim($tag), $matches) !== 1) {
				continue;
			}

			$language = $matches['language'];
			if (!empty($matches['country'])) {
				$language .= '_' . strtoupper($matches['country']);
			}

			$locales[$language] = (float)$matches['qvalue'];
		}

		arsort($locales, SORT_NUMERIC);
		return array_keys($locales);
	}
}

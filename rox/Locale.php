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
 * Locale
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Locale {

	/**
	 * Locale tag
	 * 
	 * @var string
	 */
	protected $tag = 'en_US';

	/**
	 * Returns current locale tag
	 * 
	 * @return string
	 */
	public function getTag() {
		return $this->tag;
	}

	/**
	 * Sets current locale tag
	 * 
	 * @param string $tag
	 */
	public function setTag($tag) {
		$this->tag = $tag;
	}

	/**
	 * Autodetect the locale based on browser preferences
	 *
	 * @param array $available
	 * @param bool $fallback
	 * @return boolean
	 */
	public function autoDetect($available, $fallback = true) {
		$locale = $this->getBrowserLocale();
		if ($locale === false) {
			return false;
		}

		if (in_array($locale, $available)) {
			$this->currentLanguage = $locale;
			return true;
		} 

		if ($fallback) {
			$subTags = explode('_', $locale);
			foreach ($available as $a) {
				if (strpos($a, $subTags[0]) === 0) {
					$this->tag = $a;
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Gets locale tag from browser
	 * 
	 * @return string|false
	 */
	protected function getBrowserLocale() {
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			return false;
		}

		$parts = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		if (!isset($parts[0])) {
			return false;
		}

		if (($subTags = explode('-', $parts[0])) === false) {
			return false;
		}

		if (preg_match('/^[a-z]{2,3}/', $subTags[0]) !== 1) {
			return false;
		}

		$locale = strtolower($subTags[0]);

		if (isset($subTags[1]) && preg_match('/^[A-Z]{2}$/', $subTags[1]) === 1) {
			$locale .= '_' . strtoupper($subTags[1]);
		} else if (isset($subTags[2]) && preg_match('/^[A-Z]{2}$/', $subTags[2]) === 1) {
			$locale .= '_' . strtoupper($subTags[2]);
		}

		return $locale;
	}
}
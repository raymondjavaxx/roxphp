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
 *  Validator class
 *
 * @package Rox
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Validator {

	const VALID_EMAIL_PATTERN = '/^([A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum))$/i';

	/**
	 * Validator::regexMatch()
	 *
	 * @param string $pattern
	 * @param string $subject
	 * @return boolean
	 */
	public static function regexMatch($pattern, $subject) {
		return preg_match($pattern, $subject) == 1;
	}

	/**
	 * Validator::email()
	 *
	 * @param string $subject
	 * @return boolean
	 */
	public static function email($subject) {
		return Validator::regexMatch(Validator::VALID_EMAIL_PATTERN, $subject);
	}

	/**
	 * Validator::minLength()
	 *
	 * @param string $subject
	 * @param integer $min
	 * @return boolean
	 */
	public static function minLength($subject, $min) {
		return strlen($subject) >= $min;
	}

	/**
	 * Validator::maxLength()
	 *
	 * @param string $subject
	 * @param integer $max
	 * @return boolean
	 */
	public static function maxLength($subject, $max) {
		return strlen($subject) <= $max;
	}

	/**
	 * Validator::notEmpty()
	 *
	 * @param string $subject
	 * @return boolean
	 */
	public static function notEmpty($subject) {
		$subject = trim($subject);
		return !empty($subject);
	}

	/**
	 * Validator::between()
	 *
	 * @param string $subject
	 * @param integer $min
	 * @param integer $max
	 * @return boolean
	 */
	public static function between($subject, $min, $max) {
		$len = strlen($subject);
		return (($len > $min) && ($len < $max));
	}

	/**
	 * Verifies a number using the Luhn algorithm
	 *
	 * @link http://en.wikipedia.org/wiki/Luhn_algorithm
	 * @link http://www.pat2pdf.org/pat2pdf/foo.pl?number=2950048
	 * @param string $number
	 * @return boolean
	 */
	public static function luhn($number) {
		$digits = array_reverse(str_split($number));
		$totalDigits = count($digits);

		$sum = 0;
		$alternate = false;
		for ($i=0; $i<$totalDigits; $i++) {
			$digit = $digits[$i];
			if ($alternate) {
				$digit = $digit * 2;
				if ($digit > 9) {
					$digit = $digit - 9;
				}
			}

			$sum += $digit;
			$alternate = !$alternate;
		}

		return ($sum % 10 == 0);
	}
}

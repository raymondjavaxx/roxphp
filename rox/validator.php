<?php
/**
 * Validator
 *  
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class Validator extends Object {

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
}
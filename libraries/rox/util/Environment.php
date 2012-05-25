<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2012 Ramon Torres
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\util;

class Environment {

	protected static $_current;

	public static function detect(\Closure $detector = null) {
		if (!$detector) {
			$detector = static::_detector();
		}

		static::$_current = $detector->__invoke();
		return static::$_current;
	}

	public static function is($environment) {
		return (static::$_current === $environment);
	}

	public static function set($environment) {
		static::$_current = $environment;
	}

	public static function get() {
		return static::$_current;
	}

	protected static function _detector() {
		return function() {
			$env = getenv('PHP_ROXPHP_ENV');
			if (!empty($env)) {
				return $env;
			}

			return 'development';
		};
	}
}

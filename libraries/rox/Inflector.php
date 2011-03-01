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

namespace rox;

/**
 *  Inflector
 * 
 * @package Rox
 */
class Inflector {

	/**
	 * Plural rules
	 * 
	 * @var array
	 */
	protected static $_pluralRules = array(
		'/(quiz)$/i' => '\\1zes',
		'/^(ox)$/i' => '\\1en',
		'/([m|l])ouse$/i' => '\\1ice',
		'/(matr|vert|ind)(?:ix|ex)$/i' => '\\1ices',
		'/(x|ch|ss|sh)$/i' => '\\1es',
		'/([^aeiouy]|qu)y$/i' => '\\1ies',
		'/(hive)$/i' => '\\1s',
		'/(?:([^f])fe|([lr])f)$/i' => '\\1\\2ves',
		'/sis$/i' => 'ses',
		'/([ti])um$/i' => '\\1a',
		'/(buffal|tomat)o$/i' => '\\1oes',
		'/(bu)s$/i' => '\\1ses',
		'/(alias|status)$/i' => '\\1es',
		'/(octop|vir)us$/i' => '\\1i',
		'/(ax|test)is$/i' => '\\1es',
		'/s$/i' => 's',
		'/$/' => 's',
	);

	/**
	 * Singular rules
	 * 
	 * @var array
	 */
	protected static $_singularRules = array(
		'/(database)s$/i' => '\\1',
		'/(quiz)zes$/i' => '\\1',
		'/(matr)ices$/i' => '\\1ix',
		'/(vert|ind)ices$/i' => '\\1ex',
		'/^(ox)en/i' => '\\1',
		'/(alias|status)es$/i' => '\\1',
		'/(octop|vir)i$/i' => '\\1us',
		'/(cris|ax|test)es$/i' => '\\1is',
		'/(shoe)s$/i' => '\\1',
		'/(o)es$/i' => '\\1',
		'/(bus)es$/i' => '\\1',
		'/([m|l])ice$/i' => '\\1ouse',
		'/(x|ch|ss|sh)es$/i' => '\\1',
		'/(m)ovies$/i' => '\\1ovie',
		'/(s)eries$/i' => '\\1eries',
		'/([^aeiouy]|qu)ies$/i' => '\\1y',
		'/([lr])ves$/i' => '\\1f',
		'/(tive)s$/i' => '\\1',
		'/(hive)s$/i' => '\\1',
		'/([^f])ves$/i' => '\\1fe',
		'/(^analy)ses$/i' => '\\1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis',
		'/([ti])a$/i' => '\\1um',
		'/(n)ews$/i' => '\\1ews',
		'/s$/i' => ''
	);

	/**
	 * Uncountables
	 * 
	 * @var array
	 */
	protected static $_uncountables = array(
		'equipment',
		'information',
		'rice',
		'money',
		'species',
		'series',
		'fish',
		'sheep'
	);

	protected static $_irregular = array(
		'person' => 'people',
		'child'  => 'children',
		'man'    => 'men',
		'woman'  => 'women'
	);

	/**
	 * Returns the plural form of a word
	 * 
	 * @param string $word
	 * @return string
	 */
	public static function pluralize($word) {
		if (in_array(strtolower($word), self::$_uncountables)) {
			return $word;
		}

		foreach (self::$_pluralRules as $pattern => $replacement) {
			$word = preg_replace($pattern, $replacement, $word, -1, $count);
			if ($count !== 0) {
				break;
			}
		}

		return $word;
	}

	/**
	 * Returns the singular form of a word
	 * 
	 * @param string $word
	 * @return string
	 */
	public static function singularize($word) {
		if (in_array(strtolower($word), self::$_uncountables)) {
			return $word;
		}

		foreach (self::$_singularRules as $pattern => $replacement) {
			$word = preg_replace($pattern, $replacement, $word, -1, $count);
			if ($count !== 0) {
				break;
			}
		}

		return $word;
	}

	/**
	 * Inflector::underscore()
	 * 
	 * @param string $camelCasedWord
	 * @return string
	 */
	public static function underscore($camelCasedWord) {
		$result = preg_replace('/([A-Z]+)([A-Z][a-z])/', '\\1_\\2', $camelCasedWord);
		$result = preg_replace('/([a-z\d])([A-Z])/', '\\1_\\2', $result);
		$result = str_replace('-', '_', $result);
		$result = strtolower($result);
		return $result;
	}

	/**
	 * Inflector::tableize()
	 * 
	 * @param string $modelClassName
	 * @return string
	 */
	public static function tableize($modelClassName) {
		static $cache = array();

		if (isset($cache[$modelClassName])) {
			return $cache[$modelClassName];
		}

		// for modular project
		if (preg_match('/_([a-zA-Z0-9]+)_Model_([a-zA-Z0-9]+)$/i', $modelClassName, $matches) == 1) {
			$table = self::pluralize(self::underscore(strtolower($matches[1]) . $matches[2]));
			$cache[$modelClassName] = $table;
			return $table;
		}

		// for single module project
		if (preg_match('/_Model_([a-zA-Z0-9]+)$/i', $modelClassName, $matches) == 1) {
			$table = self::pluralize(self::underscore($matches[1]));
			$cache[$modelClassName] = $table;
			return $table;
		}

		$table = self::pluralize(self::underscore($modelClassName));
		$cache[$modelClassName] = $table;
		return $table;
	}

	public static function classify($tableName) {
		return self::camelize(self::singularize($tableName));
	}

	/**
	 * undocumented function
	 *
	 * @param string $text 
	 * @return string
	 */
	public static function camelize($text) {
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $text)));
	}

	/**
	 * undocumented function
	 *
	 * @param string $text 
	 * @return string
	 */
	public static function lowerCamelize($text) {
		$text = self::camelize($text);
		return (string)strtolower(substr($text, 0, 1)) . substr($text, 1);
	}

	/**
	 * undocumented function
	 *
	 * @param string $text 
	 */
	public static function humanize($text) {
		return ucwords(str_replace('_', ' ', $text));
	}
}

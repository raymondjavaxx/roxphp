<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * HTML Helper
 *
 * @package Rox
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_Helper_Html {

	/**
	 * Rox_Helper_Html::image()
	 *
	 * @param string $path
	 * @param string $alt
	 * @param array $attributes
	 * @return string
	 */
	public function image($path, $alt = '', $attributes = array()) {
		$result = sprintf('<img src="%s" alt="%s"%s />', Rox_Router::url('/img/' . $path), $alt,
			self::_makeAttributes($attributes));
		return $result;
	}

	/**
	 * Alias for Rox_Helper_Html::image()
	 *
	 * @param string $path
	 * @param string $alt
	 * @param array $attributes
	 * @return string
	 */
	public function img($path, $alt = '', $attributes = array()) {
		return $this->image($path, $alt, $attributes);
	}

	/**
	 * HtmlHelper::css()
	 *
	 * @param mixed $file
	 * @return string
	 */
	public function css($file, $media = 'all') {
		$output = sprintf('<link rel="stylesheet" type="text/css" href="%s" media="%s" />',
			Rox_Router::url('/css/' . $file . '.css'), $media);
		return $output;
	}

	/**
	 * undocumented function
	 *
	 * @param string $file 
	 * @return string
	 */
	public function javascript($file) {
		$output = sprintf('<script type="text/javascript" src="%s"></script>',
			Rox_Router::url('/js/' . $file . '.js'));
		return $output;
	}

	/**
	 * undocumented function
	 *
	 * @param string $text 
	 * @param string $path 
	 * @param array $attributes 
	 * @return string
	 */
	public function link($text, $path, $attributes = array()) {
		$output = sprintf('<a href="%s"%s>%s</a>', Rox_Router::url($path),
			self::_makeAttributes($attributes), $text);
		return $output;
	}

	/**
	 * undocumented function
	 *
	 * @param array $attributes 
	 * @return string
	 */
	protected static function _makeAttributes(array $attributes) {
		$result = array();
		foreach ($attributes as $name => $value) {
			$result[] = ' ' . $name . '="' . $value . '"';
		}
		return implode('', $result);
	}
}

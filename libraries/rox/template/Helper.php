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

namespace rox\template;

/**
 * Abstract helper class
 *
 * @package Rox
 */
abstract class Helper {

	protected function _tag($name, $text, $attributes = array()) {
		return sprintf('<%s%s>%s</%s>', $name, $this->_attributes($attributes), $text, $name);
	}

	protected function _selfClosingTag($name, $attributes = array()) {
		return sprintf('<%s%s />', $name, $this->_attributes($attributes));
	}

	protected function _attributes($attributes) {
		if (empty($attributes)) {
			return '';
		}

		$output = array();
		foreach ($attributes as $k => $v) {
			$output[] = $k . '="' . htmlspecialchars($v) . '"';
		}

		return ' ' . implode(' ', $output);
	}
}

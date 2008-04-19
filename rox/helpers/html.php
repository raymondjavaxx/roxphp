<?php
/**
 * HtmlHelper
 *   
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */
class HtmlHelper extends Object {

  /**
   * HtmlHelper::image()
   *
   * @param string $path
   * @param string $alt
   * @return string
   */
	public function image($path, $alt = '') {
		return '<img src="' . Router::url('/img/' . $path) .  '" alt="' . $alt . '" />';
	}

  /**
   * Alias for HtmlHelper::image()
   *
   * @param string $path
   * @param string $alt
   * @return string
   */
	public function img($path, $alt = '') {
		return $this->image($path, $alt);
	}

  /**
   * HtmlHelper::css()
   *
   * @param mixed $file
   * @return
   */
	public function css($file) {
		return '<link rel="stylesheet" type="text/css" href="' . Router::url('/css/' . $file . '.css') . '" />';
	}
}
<?php
/**
 * HtmlHelper
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
class HtmlHelper extends Object {

  /**
   * HtmlHelper::image()
   *
   * @param mixed $path
   * @param string $alt
   * @return
   */
	function image($path, $alt = '') {
		return '<img src="' . Router::url('/img/' . $path) .  '" alt="{$alt}" />';
	}

  /**
   * HtmlHelper::img()
   *
   * @param mixed $path
   * @return
   */
	function img($path) {
		return $this->image($path);
	}

  /**
   * HtmlHelper::css()
   *
   * @param mixed $file
   * @return
   */
	function css($file) {
		return '<link rel="stylesheet" type="text/css" href="' . Router::url('/css/' . $file . '.css') . '" />';
	}
}
?>
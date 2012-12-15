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
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

 namespace rox\template\helper;

 use \rox\Router;
 
/**
 * Asset Helper for RoxPHP
 *
 * @package Rox
 */
class Asset extends \rox\template\Helper {

	protected static $_config = array(
		'host' => false,
		'timestamp' => true
	);

	/**
	 * Sets the configuration for the Asset Helper
	 *
	 * @param array $config key value pairs of configurations
	 *              - 'host' string: URL of the static asset host. eg: 'http://static.example.org'
	 *              - 'timestamp' boolean: Whether or not to automatically append the timestamp of
	 *                the files for cache-busting.
	 * @return void
	 */
	public static function config(array $config) {
		self::$_config = array_merge(self::$_config, $config);
	}

	/**
	 * Renders a <img> HTML tag
	 *
	 * @param string $path name of the image file
	 * @param array $attributes
	 * @return string
	 */
	public function image($path, $attributes = array()) {
		$src = $this->_src('img', $path);
		$attributes = array_merge(array('alt' => ''), $attributes, array('src' => $src));

		return $this->_selfClosingTag('img', $attributes);
	}

	/**
	 * Alias for image()
	 *
	 * @param string $path name of the image file
	 * @param array $attributes
	 * @return string
	 */
	public function img($path, $attributes = array()) {
		return $this->image($path, $attributes);
	}

	/**
	 * Creates a link element for importing external CSS files.
	 *
	 * @param string $path name of the CSS file without the extension
	 * @param string $media
	 * @return string
	 */
	public function css($path, $media = 'screen') {
		$href = $this->_src('css', $path . '.css');

		return $this->_selfClosingTag('link', array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'href' => $href,
			'media' => $media
		));
	}

	/**
	 * Creates an <script> element
	 *
	 * @param string $path name of the JS file without the extension
	 * @return string
	 */
	public function javascript($path) {
		$src = $this->_src('js', $path . '.js');

		return $this->_tag('script', '', array(
			'type' => 'text/javascript',
			'src' => $src
		));
	}

	protected function _src($folder, $filename) {
		$path = "/{$folder}/{$filename}";

		if (self::$_config['timestamp']) {
			$timestamp = @filemtime(ROX_APP_PATH . "/webroot/{$folder}/{$filename}");
			$path .= "?{$timestamp}";
		}

		if (self::$_config['host'] !== false) {
			return self::$_config['host'] . $path;
		}

		return Router::url($path);
	}
}

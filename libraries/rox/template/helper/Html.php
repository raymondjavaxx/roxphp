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

namespace rox\template\helper;

use \rox\Router;
use \rox\Inflector;
use \rox\ActiveModel;

/**
 * HTML Helper
 *
 * @package Rox
 */
class Html extends \rox\template\Helper {

	/**
	 * Renders a link element for embeding favicons
	 *
	 * @param string $path
	 * @return string
	 */
	public function favicon($path = '/favicon.ico') {
		return $this->_selfClosingTag('link', array(
			'rel' => 'shortcut icon',
			'href' => Router::url($path),
			'type' => 'image/x-icon'
		));
	}

	/**
	 * Renders a <img> HTML tag
	 *
	 * @param string $path
	 * @param array $attributes
	 * @return string
	 */
	public function image($path, $attributes = array()) {
		$attributes = array('src' => Router::url('/img/' . $path), 'alt' => '') + $attributes;
		return $this->_selfClosingTag('img', $attributes);
	}

	/**
	 * Alias for \rox\template\helper\Html::image()
	 *
	 * @param string $path
	 * @param array $attributes
	 * @return string
	 */
	public function img($path, $attributes = array()) {
		return $this->image($path, $attributes);
	}

	/**
	 * Creates a link element for importing external CSS files.
	 *
	 * @param string $file  Name of the file
	 * @param string $media
	 * @return string
	 */
	public function css($file, $media = 'all') {
		return $this->_selfClosingTag('link', array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'href' => Router::url('/css/' . $file . '.css'),
			'media' => $media
		));
	}

	/**
	 * undocumented function
	 *
	 * @param string $file 
	 * @return string
	 */
	public function javascript($file) {
		return $this->_tag('script', '', array(
			'type' => 'text/javascript',
			'src' => Router::url('/js/' . $file . '.js')
		));
	}

	/**
	 * Creates an HTML link.
	 *
	 * @param string $text 
	 * @param string $path 
	 * @param array $attributes 
	 * @return string
	 */
	public function link($text, $path, $attributes = array()) {
		$attributes = array('href' => Router::url($path)) + $attributes;
		return $this->_tag('a', $text, $attributes);
	}

	/**
	 * Creates an HTML link that points to the "view" path of a record.
	 *
	 * @param string $text 
	 * @param ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function viewLink($text, ActiveModel $object, $options = array()) {
		return $this->link($text, $this->viewPath($object, $options));
	}

	/**
	 * Creates an HTML link that points to the "edit" path of a record.
	 *
	 * @param string $text 
	 * @param ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function editLink($text, ActiveModel $object, $options = array()) {
		return $this->link($text, $this->editPath($object, $options));
	}

	/**
	 * Creates an HTML link for deleting a record.
	 *
	 * @param string $text 
	 * @param ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function deleteLink($text, ActiveModel $object, $options = array()) {
		return $this->link($text, $this->deletePath($object, $options), array('class' => 'delete'));
	}

	/**
	 * Returns the URL for viewing a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/view/[Record ID]
	 *
	 * @param ActiveModel $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function viewUrl(ActiveModel $object, $absolute = false) {
		return Router::url($this->viewPath($object), $absolute);
	}

	/**
	 * Returns the URL for editing a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/edit/[Record ID]
	 *
	 * @param ActiveModel $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function editUrl(ActiveModel $object, $absolute = false) {
		return Router::url($this->editPath($object), $absolute);
	}

	/**
	 * Returns the URL for deleting a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/delete/[Record ID]
	 *
	 * @param ActiveModel $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function deleteUrl(ActiveModel $object, $absolute = false) {
		return Router::url($this->deletePath($object), $absolute);
	}

	/**
	 * Returns the path for viewing a record.
	 *
	 * @param ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function viewPath($object, $options = array()) {
		$path = "/" . self::_controllerNameFromModel($object) . "/" . $object->getId();
		return isset($options['namespace']) ? '/' . $options['namespace'] . $path : $path;
	}

	/**
	 * Returns the path for editing a record.
	 *
	 * @param ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function editPath($object, $options = array()) {
		$path = "/" . self::_controllerNameFromModel($object) . "/" . $object->getId() . "/edit";
		return isset($options['namespace']) ? '/' . $options['namespace'] . $path : $path;
	}

	/**
	 * Returns the path for deleting a record.
	 *
	 * @param ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function deletePath($object, $options = array()) {
		$path = "/" . self::_controllerNameFromModel($object) . "/" . $object->getId();
		return isset($options['namespace']) ? '/' . $options['namespace'] . $path : $path;
	}

	/**
	 * Returns the controller name for a given ActiveModel instance.
	 *
	 * @param ActiveModel $object 
	 * @return string
	 */
	protected static function _controllerNameFromModel(ActiveModel $object) {
		static $results = array();
		$class = get_class($object);
		if (!isset($results[$class])) {
			$results[$class] = Inflector::underscore(Inflector::pluralize($class));
		}
		return $results[$class];
	}
}

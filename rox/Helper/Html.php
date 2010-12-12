<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * HTML Helper
 *
 * @package Rox
 */
class Rox_Helper_Html {

	/**
	 * Renders a link element for embeding favicons
	 *
	 * @param string $path
	 * @return string
	 */
	public function favicon($path = '/favicon.ico') {
		$result = sprintf('<link rel="shortcut icon" href="%s" type="image/x-icon" />',
			Rox_Router::url($path));
		return $result;
	}

	/**
	 * Renders a <img> HTML tag
	 *
	 * @param string $path
	 * @param array $attributes
	 * @return string
	 */
	public function image($path, $attributes = array()) {
		$attributes += array('alt' => '');
		$result = sprintf('<img src="%s"%s />', Rox_Router::url('/img/' . $path),
			self::_makeAttributes($attributes));
		return $result;
	}

	/**
	 * Alias for Rox_Helper_Html::image()
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
	 * Creates an HTML link.
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
	 * Creates an HTML link that points to the "view" path of a record.
	 *
	 * @param string $text 
	 * @param Rox_ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function viewLink($text, Rox_ActiveModel $object, $options = array()) {
		return $this->link($text, $this->viewPath($object, $options));
	}

	/**
	 * Creates an HTML link that points to the "edit" path of a record.
	 *
	 * @param string $text 
	 * @param Rox_ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function editLink($text, Rox_ActiveModel $object, $options = array()) {
		return $this->link($text, $this->editPath($object, $options));
	}

	/**
	 * Creates an HTML link for deleting a record.
	 *
	 * @param string $text 
	 * @param Rox_ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function deleteLink($text, Rox_ActiveModel $object, $options = array()) {
		return $this->link($text, $this->deletePath($object, $options), array('class' => 'delete'));
	}

	/**
	 * Returns the URL for viewing a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/view/[Record ID]
	 *
	 * @param Rox_ActiveModel $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function viewUrl(Rox_ActiveModel $object, $absolute = false) {
		return Rox_Router::url($this->viewPath($object), $absolute);
	}

	/**
	 * Returns the URL for editing a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/edit/[Record ID]
	 *
	 * @param Rox_ActiveModel $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function editUrl(Rox_ActiveModel $object, $absolute = false) {
		return Rox_Router::url($this->editPath($object), $absolute);
	}

	/**
	 * Returns the URL for deleting a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/delete/[Record ID]
	 *
	 * @param Rox_ActiveModel $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function delteUrl(Rox_ActiveModel $object, $absolute = false) {
		return Rox_Router::url($this->deletePath($object), $absolute);
	}

	/**
	 * Returns the path for viewing a record.
	 *
	 * @param Rox_ActiveModel $object 
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
	 * @param Rox_ActiveModel $object 
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
	 * @param Rox_ActiveModel $object 
	 * @param array $options
	 * @return string
	 */
	public function deletePath($object, $options = array()) {
		$path = "/" . self::_controllerNameFromModel($object) . "/" . $object->getId();
		return isset($options['namespace']) ? '/' . $options['namespace'] . $path : $path;
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
			$result[] = ' ' . $name . '="' . htmlspecialchars($value) . '"';
		}
		return implode('', $result);
	}

	/**
	 * Returns the controller name for a given ActiveModel instance.
	 *
	 * @param Rox_ActiveModel $object 
	 * @return string
	 */
	protected static function _controllerNameFromModel(Rox_ActiveModel $object) {
		static $results = array();
		$class = get_class($object);
		if (!isset($results[$class])) {
			$results[$class] = Rox_Inflector::underscore(Rox_Inflector::pluralize($class));
		}
		return $results[$class];
	}
}

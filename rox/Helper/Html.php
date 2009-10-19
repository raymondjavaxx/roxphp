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
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	public function viewLink($text, Rox_ActiveRecord $object) {
		return $this->link($text, $this->viewPath($object));
	}

	/**
	 * Creates an HTML link that points to the "edit" path of a record.
	 *
	 * @param string $text 
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	public function editLink($text, Rox_ActiveRecord $object) {
		return $this->link($text, $this->editPath($object));
	}

	/**
	 * Creates an HTML link for deleting a record.
	 *
	 * @param string $text 
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	public function deleteLink($text, Rox_ActiveRecord $object) {
		return $this->link($text, $this->deletePath($object), array('class' => 'delete'));
	}

	/**
	 * Returns the URL for viewing a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/view/[Record ID]
	 *
	 * @param Rox_ActiveRecord $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function viewUrl(Rox_ActiveRecord $object, $absolute = false) {
		return Rox_Router::url($this->viewPath($object), $absolute);
	}

	/**
	 * Returns the URL for editing a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/edit/[Record ID]
	 *
	 * @param Rox_ActiveRecord $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function editUrl(Rox_ActiveRecord $object, $absolute = false) {
		return Rox_Router::url($this->editPath($object), $absolute);
	}

	/**
	 * Returns the URL for deleting a record.
	 *
	 * If a record of class Account is passed
	 * the returned url is: [...]/accounts/delete/[Record ID]
	 *
	 * @param Rox_ActiveRecord $object 
	 * @param string $absolute 
	 * @return string
	 */
	public function delteUrl(Rox_ActiveRecord $object, $absolute = false) {
		return Rox_Router::url($this->deletePath($object), $absolute);
	}

	/**
	 * Returns the path for viewing a record.
	 *
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	public function viewPath($object) {
		return "/" . self::_controllerFromActiveRecord($object) . "/view/" . $object->getId();
	}

	/**
	 * Returns the path for editing a record.
	 *
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	public function editPath($object) {
		return "/" . self::_controllerFromActiveRecord($object) . "/edit/" . $object->getId();
	}

	/**
	 * Returns the path for deleting a record.
	 *
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	public function deletePath($object) {
		return "/" . self::_controllerFromActiveRecord($object) . "/delete/" . $object->getId();
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

	/**
	 * Returns the controller name for a given ActiveRecord instance.
	 *
	 * @param Rox_ActiveRecord $object 
	 * @return string
	 */
	protected static function _controllerFromActiveRecord($object) {
		static $results = array();
		$class = get_class($object);
		if (!isset($results[$class])) {
			$results[$class] = Rox_Inflector::underscore(Rox_Inflector::pluralize($class));
		}
		return $results[$class];
	}
}

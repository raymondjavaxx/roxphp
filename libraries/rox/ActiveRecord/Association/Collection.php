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
 * Collection Assoc.
 *
 * @package Rox
 */
class Rox_ActiveRecord_Association_Collection implements IteratorAggregate {

	protected $_scope = array();

	/**
	 * Model instance
	 *
	 * @var Rox_ActiveRecord
	 */
	protected $_model;

	public function __construct($model, $scope) {
		$this->_model = $model;
		$this->_scope = $scope;
	}

	public function build($attributes = array()) {
		$other = new $this->_model;
		$other->setData($attributes);

		foreach ($this->_scope as $attribute => $value) {
			$other->{$attribute} = $value;
		}

		return $other;
	}

	public function find($ids, $options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model::find($ids, $options);
	}

	public function findFirst($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model::findFirst($options);
	}

	public function findLast($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model::findLast($options);
	}

	public function findAll($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model::findAll($options);
	}

	public function deleteAll($conditions = array()) {
		$conditions = array_merge((array)$conditions, $this->_scope);
		$this->_model::deleteAll($conditions);
	}

	public function findCount($conditions = array()) {
		$conditions = array_merge((array)$conditions, $this->_scope);
		return $this->_model::findCount($conditions);
	}

	public function paginate($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model::paginate($options);
	}

	public function getIterator() {
		return new ArrayIterator($this->findAll());
	}
}

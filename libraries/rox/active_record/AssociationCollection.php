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

namespace rox\active_record;
use rox\Inflector;

/**
 * Collection Assoc.
 *
 * @package Rox
 */
class AssociationCollection implements \IteratorAggregate {

	protected $_scope = array();
	
	protected $_through;

	/**
	 * Model name
	 *
	 * @var string
	 */
	protected $_model;

	public function __construct($model, $scope, $through = null) {
		$this->_model = $model;
		$this->_scope = $scope;
		$this->_through = $through;
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
		$model = $this->_model;
		return $model::find($ids, $options);
	}

	public function findFirst($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		$model = $this->_model;
		return $model::findFirst($options);
	}

	public function findLast($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		$model = $this->_model;
		return $model::findLast($options);
	}

	public function findAll($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		$model = $this->_model;

		if ($this->_through) {
			$through_model = $this->_through;
			return self::_handleThrough($through_model::findAll($options), $model, $this->_scope);
		}

		return $model::findAll($options);
	}

	public function deleteAll($conditions = array()) {
		$conditions = array_merge((array)$conditions, $this->_scope);
		$model = $this->_model;
		$model::deleteAll($conditions);
	}

	public function findCount($conditions = array()) {
		$conditions = array_merge((array)$conditions, $this->_scope);
		$model = $this->_model;
		return $model::findCount($conditions);
	}

	public function paginate($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		$model = $this->_model;
		return $model::paginate($options);
	}

	public function getIterator() {
		return new \ArrayIterator($this->findAll());
	}
	
	private function _handleThrough($through, $model, $scope){
		$model_var = Inflector::singularize(Inflector::tableize($model));
		
		$assoc = array();
		foreach($through as $item){
			$assoc[] = $model::find($item->{$model_var . '_id'});
		}
		
		return $assoc;
	}
}

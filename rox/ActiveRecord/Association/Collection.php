<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2010 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Collection Assoc.
 *
 * @package Rox
 * @copyright Copyright (C) 2008 - 2010 Ramon Torres
 * @license http://roxphp.com/static/license.html
 */
class Rox_ActiveRecord_Association_Collection {

	protected $_scope = array();

	/**
	 * Model instance
	 *
	 * @var Rox_ActiveRecord
	 */
	protected $_model;

	public function __construct($class, $scope) {
		$this->_model = Rox_ActiveRecord::model($class);
		$this->_scope = $scope;
	}

	public function find($ids, $options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model->find($ids, $options);
	}

	public function findFirst($options) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model->findFirst($options);
	}

	public function findLast($options) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model->findLast($options);
	}

	public function findAll($options = array()) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model->findAll($options);
	}

	public function deleteAll($conditions = array()) {
		$conditions = array_merge((array)$conditions, $this->_scope);
		$this->_model->deleteAll($conditions);
	}

	public function findCount($conditions = array()) {
		$conditions = array_merge((array)$conditions, $this->_scope);
		return $this->_model->findCount($conditions);
	}

	public function paginate($options) {
		$options = array_merge_recursive($options, array('conditions' => $this->_scope));
		return $this->_model->paginate($options);
	}
}

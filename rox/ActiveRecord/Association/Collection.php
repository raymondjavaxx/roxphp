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
	protected $_model;

	public function __construct($class, $scope) {
		$this->_model = Rox_ActiveRecord::model($class);
		$this->_scope = $scope;
	}

	public function find($conditions = array(), $options = array()) {
		$options += array('conditions' => $this->_scope);
		return $this->_model->find($conditions, $options);
	}

	public function findAll($conditions = array(), $options = array()) {
		$conditions += $this->_scope;
		return $this->_model->findAll($conditions, $options);
	}

	public function deleteAll($condtions = array()) {
		$conditions += $this->_scope;
		$this->_model->deleteAll($condtions);
	}
}

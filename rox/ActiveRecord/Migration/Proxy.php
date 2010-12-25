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
 * Migration Proxy class
 *
 * @package Rox
 */
class Rox_ActiveRecord_Migration_Proxy {

	protected $_target;

	protected $_calls = array();

	public function __construct($target = null) {
		$this->_target = $target;
	}

	public function __call($method, $args) {
		$this->_calls[] = compact('method', 'args');
	}

	public function inverse() {
		$calls = array_reverse($this->_calls);
		foreach ($calls as &$call) {
			$call = $this->inverseOf($call['method'], $call['args']);
		}

		return $calls;
	}

	public function inverseOf($method, $args) {
		switch ($method) {
			case 'addColumn':
				return array(
					'method' => 'removecolumn',
					'args' => array($args[0], $args[1])
				);
				break;

			case 'createTable':
				return array(
					'method' => 'dropTable',
					'args' => array($args[0])
				);
				break;

			case 'renameColumn':
				return array(
					'method' => 'dropTable',
					'args' => array($args[1], $args[0])
				);
				break;

			default:
				throw new Rox_Exception("Irreversible migration command {$method}");
				break;
		}
	}

	public function playReversed() {
		$calls = $this->inverse();

		foreach ($calls as $call) {
			call_user_func_array(array($this->_target, $call['method']), $call['args']);
		}
	}

	public function createTable() {
		$method = 'createTable';
		$args = func_get_args();
		$this->_calls[] = compact('method', 'args');

		$class = __CLASS__;
		return new $class;
	}
}

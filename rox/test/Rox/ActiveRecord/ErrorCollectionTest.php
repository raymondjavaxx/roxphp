<?php

require_once dirname(dirname(dirname(__FILE__))) . '/helper.php';

class Rox_ActiveRecord_ErrorCollectionTest extends PHPUnit_Framework_TestCase {

	public function testAdd() {
		$collection = new Rox_ActiveRecord_ErrorCollection;
		$collection->add('username', 'Invalid username');
		$result = $collection->toArray();
		$expected = array('username' => 'Invalid username');
		$this->assertSame($expected, $result);

		$collection = new Rox_ActiveRecord_ErrorCollection;
		$collection->add('email');
		$result = $collection->toArray();
		$expected = array('email' => 'Email is invalid');
		$this->assertSame($expected, $result);
	}

	public function testClear() {
		$collection = new Rox_ActiveRecord_ErrorCollection;

		$collection->add('username', 'Invalid username');
		$result = $collection->toArray();
		$expected = array('username' => 'Invalid username');
		$this->assertSame($expected, $result);

		$collection->clear();
		$result = $collection->toArray();
		$expected = array();
		$this->assertSame($expected, $result);
	}
}

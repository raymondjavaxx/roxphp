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
 * @package \rox\test
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\test\cases\active_model;

use \rox\active_model\ErrorCollection;

/**
 * Test case for \rox\active_model\ErrorCollection
 *
 * @package \rox\test
 */
class ErrorCollectionTest extends \PHPUnit_Framework_TestCase {

	public function testAdd() {
		$collection = new ErrorCollection;
		$collection->add('username', 'Invalid username');
		$result = $collection->toArray();
		$expected = array('username' => 'Invalid username');
		$this->assertSame($expected, $result);

		$collection = new ErrorCollection;
		$collection->add('email');
		$result = $collection->toArray();
		$expected = array('email' => 'Email is invalid');
		$this->assertSame($expected, $result);
	}

	public function testClear() {
		$collection = new ErrorCollection;

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

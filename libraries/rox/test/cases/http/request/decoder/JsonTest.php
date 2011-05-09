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

namespace rox\test\cases\http\request\decoder;

use \rox\http\request\decoder\Json;

/**
 * Test case for \rox\http\request\decoder\Json
 *
 * @package \rox\test
 */
class JsonTest extends \PHPUnit_Framework_TestCase {

	public function testDecode() {
		$data = '{"country":{"tax":0.2, "code": "FR"}}';
		$decoder = new Json;
		$result = $decoder->decode($data);

		$expected = array('country' => array('tax'  => 0.2, 'code' => 'FR'));

		$this->assertEquals($expected, $result);
	}

	public function testDecodeWithInvalidData() {
		$this->setExpectedException('\rox\Exception');
		$data = '{"country", 64]'; // invalid json
		$decoder = new Json;
		$result = $decoder->decode($data);
	}
}

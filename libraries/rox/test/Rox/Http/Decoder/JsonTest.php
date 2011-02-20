<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/helper.php';

class Rox_Http_Decoder_JsonTest extends PHPUnit_Framework_TestCase {

	public function testDecode() {
		$data = '{"country":{"tax":0.2, "code": "FR"}}';
		$decoder = new Rox_Http_Decoder_Json;
		$result = $decoder->decode($data);

		$expected = array('country' => array('tax'  => 0.2, 'code' => 'FR'));

		$this->assertEquals($expected, $result);
	}

	public function testDecodeWithInvalidData() {
		$this->setExpectedException('Rox_Exception');
		$data = '{"country", 64]'; // invalid json
		$decoder = new Rox_Http_Decoder_Json;
		$result = $decoder->decode($data);
	}
}

<?php

require_once dirname(dirname(__FILE__)) . '/helper.php';

class Rox_LocaleMock extends Rox_Locale {

	public static function detectBrowserLocales() {
		return self::_detectBrowserLocales();
	}
}

class Rox_LocaleTest extends PHPUnit_Framework_TestCase {

	protected $_originalSuperGlobals = array();

	public function setUp() {
		$this->_originalSuperGlobals = array(
			'SERVER' => $_SERVER
		);
	}

	public function tearDown() {
		$_SERVER = $this->_originalSuperGlobals['SERVER'];
	}

	public function testDetectBrowserLocales() {
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en-gb;q=0.8, da, en;q=0.7";
		$result = Rox_LocaleMock::detectBrowserLocales();
		$expected = array('da', 'en_GB', 'en');
		$this->assertSame($expected, $result);

		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es-do, es, en;q=0.7";
		$result = Rox_LocaleMock::detectBrowserLocales();
		$expected = array('es_DO', 'es', 'en');
		$this->assertSame($expected, $result);

		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es-do, es;q=0.5, en;q=0.7";
		$result = Rox_LocaleMock::detectBrowserLocales();
		$expected = array('es_DO', 'en', 'es');
		$this->assertSame($expected, $result);
	}

	public function testAutoDetect() {
		$locale = new Rox_LocaleMock;
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en-gb;q=0.8, da, en;q=0.7";
		$result = $locale->autoDetect(array('es_DO', 'en'));
		$this->assertTrue($result);
		$this->assertSame('en', $locale->getTag());

		$locale = new Rox_LocaleMock;
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "da, en-us;q=0.5";
		$result = $locale->autoDetect(array('es_DO', 'en'));
		$this->assertFalse($result);
		$this->assertSame('en_US', $locale->getTag());
	}

	public function testAutoDetectFallback() {
		$locale = new Rox_LocaleMock;
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es-es, en-us;q=0.5";
		$result = $locale->autoDetect(array('es_DO', 'it'), true);
		$this->assertTrue($result);
		$this->assertSame('es_DO', $locale->getTag());
	}
}

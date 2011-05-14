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

namespace rox\test\cases\g11n;

use \rox\g11n\Locale;

class LocaleMock extends Locale {
	public static function detectBrowserLocales() {
		return self::_detectBrowserLocales();
	}
}

/**
 * Test case for \rox\g11n\Locale
 *
 * @package \rox\test
 */
class LocaleTest extends \PHPUnit_Framework_TestCase {

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
		$result = LocaleMock::detectBrowserLocales();
		$expected = array('da', 'en_GB', 'en');
		$this->assertSame($expected, $result);

		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es-do, es, en;q=0.7";
		$result = LocaleMock::detectBrowserLocales();
		$expected = array('es_DO', 'es', 'en');
		$this->assertSame($expected, $result);

		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es-do, es;q=0.5, en;q=0.7";
		$result = LocaleMock::detectBrowserLocales();
		$expected = array('es_DO', 'en', 'es');
		$this->assertSame($expected, $result);
	}

	public function testAutoDetect() {
		$locale = new LocaleMock;
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "en-gb;q=0.8, da, en;q=0.7";
		$result = $locale->autoDetect(array('es_DO', 'en'));
		$this->assertTrue($result);
		$this->assertSame('en', $locale->getTag());

		$locale = new LocaleMock;
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "da, en-us;q=0.5";
		$result = $locale->autoDetect(array('es_DO', 'en'));
		$this->assertFalse($result);
		$this->assertSame('en_US', $locale->getTag());
	}

	public function testAutoDetectFallback() {
		$locale = new LocaleMock;
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = "es-es, en-us;q=0.5";
		$result = $locale->autoDetect(array('es_DO', 'it'), true);
		$this->assertTrue($result);
		$this->assertSame('es_DO', $locale->getTag());
	}
}

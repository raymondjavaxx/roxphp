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

namespace rox\test\cases\http;

use \rox\http\Request;

/**
 * Test case for \rox\http\Request
 *
 * @package \rox\test
 */
class RequestTest extends \PHPUnit_Framework_TestCase {

	protected $_originalSuperGlobals = array();

	public function setUp() {
		$this->_originalSuperGlobals = array(
			'SERVER' => $_SERVER
		);

	}

	public function tearDown() {
		$_SERVER = $this->_originalSuperGlobals['SERVER'];
	}

	public function testIsAjax() {
		$request = new Request;
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->assertTrue($request->isAjax());

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'randomthing';
		$this->assertFalse($request->isAjax());

		unset($_SERVER['HTTP_X_REQUESTED_WITH']);
		$this->assertFalse($request->isAjax());
	}

	public function testIsSSL() {
		$request = new Request;
		$_SERVER['HTTPS'] = true;
		$this->assertTrue($request->isSSL());

		$_SERVER['HTTPS'] = false;
		$this->assertFalse($request->isSSL());

		// I <3 You IIS...
		$_SERVER['HTTPS'] = 'on';
		$this->assertTrue($request->isSSL());

		$_SERVER['HTTPS'] = 'off';
		$this->assertFalse($request->isSSL());
	}

	public function testIsIphone() {
		$request = new Request;
		$_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A537a Safari/419.3";
		$this->assertTrue($request->isIphone());

		$_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A100a Safari/419.3";
		$this->assertTrue($request->isIphone());

		// Firefox on Windows 7
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 GTB6 (.NET CLR 3.5.30729)';
		$this->assertFalse($request->isIphone());
	}
}

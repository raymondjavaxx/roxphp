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
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$request = new Request;
		$this->assertTrue($request->isAjax());

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'randomthing';
		$request = new Request;
		$this->assertFalse($request->isAjax());

		unset($_SERVER['HTTP_X_REQUESTED_WITH']);
		$request = new Request;
		$this->assertFalse($request->isAjax());
	}

	public function testIsSSL() {
		$_SERVER['HTTPS'] = true;
		$request = new Request;
		$this->assertTrue($request->isSSL());

		$_SERVER['HTTPS'] = false;
		$request = new Request;
		$this->assertFalse($request->isSSL());

		// I <3 You IIS...
		$_SERVER['HTTPS'] = 'on';
		$request = new Request;
		$this->assertTrue($request->isSSL());

		$_SERVER['HTTPS'] = 'off';
		$request = new Request;
		$this->assertFalse($request->isSSL());
	}

	public function testIsIphone() {
		$_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A537a Safari/419.3";
		$request = new Request;
		$this->assertTrue($request->isIphone());

		$_SERVER['HTTP_USER_AGENT'] = "Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A100a Safari/419.3";
		$request = new Request;
		$this->assertTrue($request->isIphone());

		// Firefox on Windows 7
		$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 GTB6 (.NET CLR 3.5.30729)';
		$request = new Request;
		$this->assertFalse($request->isIphone());
	}
}

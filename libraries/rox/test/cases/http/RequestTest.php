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
		$request = Request::fromGlobals();
		$this->assertTrue($request->isSSL());

		$_SERVER['HTTPS'] = false;
		$request = Request::fromGlobals();
		$this->assertFalse($request->isSSL());

		// For Microsoft server
		$_SERVER['HTTPS'] = 'on';
		$request = Request::fromGlobals();
		$this->assertTrue($request->isSSL());

		$_SERVER['HTTPS'] = 'off';
		$request = Request::fromGlobals();
		$this->assertFalse($request->isSSL());
	}
}

<?php

require_once dirname(dirname(__FILE__)) . '/helper.php';

class Rox_RouterTest extends PHPUnit_Framework_TestCase {

	protected $_originalSuperGlobals = array();

	public function setUp() {
		$this->_originalSuperGlobals = array(
			'SERVER' => $_SERVER
		);
	}

	public function tearDown() {
		$_SERVER = $this->_originalSuperGlobals['SERVER'];
	}

	public function testParseUrl() {
		$result = Rox_Router::parseUrl('/users/edit/23');
		$expected = array(
			'controller' => 'users',
			'controller_class' => 'UsersController',
			'action' => 'edit',
			'action_method' => 'editAction',
			'params' => array('23'),
			'prefix' => null,
			'extension' => 'html'
		);

		$this->assertEquals($expected, $result);
	}

	public function testParseUrlWithPrefix() {
		Rox_Router::setConfig(array('prefixes' => array('admin')));

		$result = Rox_Router::parseUrl('/admin/users/edit/23');
		$expected = array(
			'controller' => 'users',
			'controller_class' => 'UsersController',
			'action' => 'edit',
			'action_method' => 'adminEditAction',
			'params' => array('23'),
			'prefix' => 'admin',
			'extension' => 'html'
		);

		$this->assertEquals($expected, $result);
	}

	public function testParseUrlWithDefaultAction() {
		$result = Rox_Router::parseUrl('/users');
		$expected = array(
			'controller' => 'users',
			'controller_class' => 'UsersController',
			'action' => 'index',
			'action_method' => 'indexAction',
			'params' => array(),
			'prefix' => null,
			'extension' => 'html'
		);

		$this->assertEquals($expected, $result);
	}

	public function testParseUrlWithExtension() {
		$result = Rox_Router::parseUrl('/articles.json');
		$expected = array(
			'controller' => 'articles',
			'controller_class' => 'ArticlesController',
			'action' => 'index',
			'action_method' => 'indexAction',
			'params' => array(),
			'prefix' => null,
			'extension' => 'json'
		);

		$this->assertEquals($expected, $result);

		$result = Rox_Router::parseUrl('/articles/view/3.xml');
		$expected = array(
			'controller' => 'articles',
			'controller_class' => 'ArticlesController',
			'action' => 'view',
			'action_method' => 'viewAction',
			'params' => array('3'),
			'prefix' => null,
			'extension' => 'xml'
		);

		$this->assertEquals($expected, $result);
	}


	public function testParseUrlWithInvalidController() {
		$this->setExpectedException('Exception');
		Rox_Router::parseUrl('/articl..es');
	}

	public function testParseUrlWithInvalidAction() {
		$this->setExpectedException('Exception');
		Rox_Router::parseUrl('/articles/ed-it');
	}

	public function testBase() {
		$_SERVER['PHP_SELF'] = '/folder/app/webroot/index.php';
		$result = Rox_Router::base();
		$this->assertSame('/folder', $result);
	}

	public function testGetBaseUrl() {
		$_SERVER['HTTP_HOST'] = 'example.org';
		$result = Rox_Router::getBaseUrl();
		$this->assertSame('http://example.org', $result);
	}

	public function testUrl() {
		$_SERVER['PHP_SELF'] = '/folder/app/webroot/index.php';
		$_SERVER['HTTP_HOST'] = 'example.org';

		$result = Rox_Router::url('/articles');
		$this->assertSame('/folder/articles', $result);

		$result = Rox_Router::url('/articles', true);
		$this->assertSame('http://example.org/folder/articles', $result);
	}
}

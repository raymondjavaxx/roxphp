<?php

require_once dirname(dirname(__FILE__)) . '/helper.php';

class Rox_RouterTest extends PHPUnit_Framework_TestCase {

	protected $_originalSuperGlobals = array();

	public function setUp() {
		$this->_originalSuperGlobals = array('SERVER' => $_SERVER);
	}

	public function tearDown() {
		$_SERVER = $this->_originalSuperGlobals['SERVER'];
	}

	public function testParseUrl() {
		Rox_Router::resource('companies');
		Rox_Router::resource('companies.people');
		Rox_Router::connect('/:controller/:action/:id', array());
		Rox_Router::connect('/:controller/:action', array());
		Rox_Router::connect('/:controller', array('action' => 'index'));

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Rox_Router::parseUrl('/companies');
		$expected = array(
			'controller' => 'companies',
			'controller_class' => 'CompaniesController',
			'action' => 'index',
			'action_method' => 'indexAction',
			'args' => array(),
			'namespace' => false,
			'extension' => 'html'
		);
		$this->assertEquals($expected, $result);

		$_SERVER['REQUEST_METHOD'] = 'POST';
		$result = Rox_Router::parseUrl('/companies');
		$expected = array(
			'controller' => 'companies',
			'controller_class' => 'CompaniesController',
			'action' => 'add',
			'action_method' => 'addAction',
			'args' => array(),
			'namespace' => false,
			'extension' => 'html'
		);
		$this->assertEquals($expected, $result);

		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$result = Rox_Router::parseUrl('/companies/1');
		$expected = array(
			'controller' => 'companies',
			'controller_class' => 'CompaniesController',
			'action' => 'edit',
			'action_method' => 'editAction',
			'args' => array('id' => '1'),
			'namespace' => false,
			'extension' => 'html'
		);
		$this->assertEquals($expected, $result);

		$_SERVER['REQUEST_METHOD'] = 'DELETE';
		$result = Rox_Router::parseUrl('/companies/1');
		$expected = array(
			'controller' => 'companies',
			'controller_class' => 'CompaniesController',
			'action' => 'delete',
			'action_method' => 'deleteAction',
			'args' => array('id' => '1'),
			'namespace' => false,
			'extension' => 'html'
		);
		$this->assertEquals($expected, $result);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Rox_Router::parseUrl('/companies/1.json');
		$expected = array(
			'controller' => 'companies',
			'controller_class' => 'CompaniesController',
			'action' => 'view',
			'action_method' => 'viewAction',
			'args' => array('id' => '1'),
			'namespace' => false,
			'extension' => 'json'
		);
		$this->assertEquals($expected, $result);

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Rox_Router::parseUrl('/companies/1/people/25.xml');
		$expected = array(
			'controller' => 'people',
			'controller_class' => 'PeopleController',
			'action' => 'view',
			'action_method' => 'viewAction',
			'args' => array('company_id' => '1', 'id' => '25'),
			'namespace' => false,
			'extension' => 'xml'
		);
		$this->assertEquals($expected, $result);
	
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Rox_Router::parseUrl('/users/edit/23');
		$expected = array(
			'controller' => 'users',
			'controller_class' => 'UsersController',
			'action' => 'edit',
			'action_method' => 'editAction',
			'args' => array('id' => '23'),
			'namespace' => false,
			'extension' => 'html'
		);
		$this->assertEquals($expected, $result);
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

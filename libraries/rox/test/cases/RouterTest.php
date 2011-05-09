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

namespace rox\test\cases;

use \rox\Router;
use \rox\http\Request;

/**
 * Test case for \rox\Router
 *
 * @package \rox\test
 */
class RouterTest extends \PHPUnit_Framework_TestCase {

	protected $_originalSuperGlobals = array();

	public function setUp() {
		$this->_originalSuperGlobals = array('SERVER' => $_SERVER);
	}

	public function tearDown() {
		$_SERVER = $this->_originalSuperGlobals['SERVER'];
	}

	public function testParseUrl() {
		Router::resource('companies');
		Router::resource('companies.people');
		Router::connect('/:controller/:action/:id', array());
		Router::connect('/:controller/:action', array());
		Router::connect('/:controller', array('action' => 'index'));

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$result = Router::parseUrl('/companies', new Request);
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
		$result = Router::parseUrl('/companies', new Request);
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
		$result = Router::parseUrl('/companies/1');
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
		$result = Router::parseUrl('/companies/1', new Request);
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
		$result = Router::parseUrl('/companies/1.json', new Request);
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
		$result = Router::parseUrl('/companies/1/people/25.xml', new Request);
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
		$result = Router::parseUrl('/users/edit/23', new Request);
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
		$result = Router::base();
		$this->assertSame('/folder', $result);
	}

	public function testGetBaseUrl() {
		$_SERVER['HTTP_HOST'] = 'example.org';
		$result = Router::getBaseUrl();
		$this->assertSame('http://example.org', $result);
	}

	public function testUrl() {
		$_SERVER['PHP_SELF'] = '/folder/app/webroot/index.php';
		$_SERVER['HTTP_HOST'] = 'example.org';

		$result = Router::url('/articles');
		$this->assertSame('/folder/articles', $result);

		$result = Router::url('/articles', true);
		$this->assertSame('http://example.org/folder/articles', $result);
	}
}

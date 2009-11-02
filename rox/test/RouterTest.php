<?php

require_once '../Router.php';
require_once '../Inflector.php';

class RouterTest extends PHPUnit_Framework_TestCase {

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
}

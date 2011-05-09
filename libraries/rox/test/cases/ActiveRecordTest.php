<?php

namespace rox\test\cases;

use rox\active_record\ConnectionManager;
use rox\test\mocks\models\UserMock;

class ActiveRecordTest extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		ConnectionManager::setConfig('rox-test', array(
			'class' => '\rox\test\mocks\active_record\DataSource'
		));
	}

	public function testConstructor() {
		$user = new UserMock(array(
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'john@example.org'
		));

		$this->assertEquals('John', $user->first_name);
		$this->assertEquals('Doe', $user->last_name);
		$this->assertEquals('john@example.org', $user->email);
	}

	public function testCreate() {
		$jose = UserMock::create(array(
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'john@example.org',
			'password' => 'test'
		));

		$this->assertTrue($jose instanceof UserMock);
		$this->assertTrue(is_numeric($jose->getId()));
	}

	public function testProtectedFields() {
		$user = new UserMock(array(
			'first_name' => 'Jane',
			'last_name'  => 'Doe',
			'email'      => 'jane@example.com',
			'role'       => 'Admin'
		));

		$this->assertEquals(null, $user->getData('role'));

		$user->setData('role', 'Admin');
		$this->assertEquals('Admin', $user->getData('role'));
	}

	public function testBuildConditionsSQL() {
		$user = new UserMock;
		$result = $user->invokeMethod('_buildConditionsSQL', array('first_name' => 'John'));
		$expected = " WHERE `first_name` = 'John'";
		$this->assertEquals($expected, $result);

		$result = $user->invokeMethod('_buildConditionsSQL', array('first_name' => 'John', 'role' => 'Admin'));
		$expected = " WHERE `first_name` = 'John' AND `role` = 'Admin'";
		$this->assertEquals($expected, $result);

		$result = $user->invokeMethod('_buildConditionsSQL', '`id` = 40');
		$expected = " WHERE `id` = 40";
		$this->assertEquals($expected, $result);
	}

	public function testValidatesPresenceOf() {
		$user = new UserMock;
		$user->valid();

		$user->invokeMethod('_validatesPresenceOf', 'first_name');
		$result = $user->getValidationErrors();
		$expected = array('first_name' => 'cannot be left blank');
		$this->assertEquals($expected, $result);

		$user->invokeMethod('_validatesPresenceOf', 'first_name', 'custom message');
		$result = $user->getValidationErrors();
		$expected = array('first_name' => 'custom message');
		$this->assertEquals($expected, $result);

		$user->invokeMethod('_validatesPresenceOf', array('first_name', 'last_name', 'password'));
		$result = $user->getValidationErrors();
		$expected = array(
			'first_name' => 'cannot be left blank',
			'last_name' => 'cannot be left blank',
			'password' => 'cannot be left blank'
		);

		$this->assertEquals($expected, $result);
	}
}

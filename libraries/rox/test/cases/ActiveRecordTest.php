<?php
/*
require_once dirname(dirname(__FILE__)) . '/helper.php';

Rox_ConnectionManager::setConfig('rox-test', array(
	'database' => 'rox_test'
));

class User extends Rox_ActiveRecord {

	protected static $_dataSourceName = 'rox-test';

	protected static $_protectedAttributes = array('role');

	protected function _beforeSave() {
		if ($this->_newRecord) {
			$this->password = md5($this->password);
		}
	}

	public function invokeMethod($method) {
		$params = array_slice(func_get_args(), 1);
		return call_user_func_array(array($this, $method), $params);
	}
}

class ActiveRecordTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		try {
			$ds = Rox_ConnectionManager::getDataSource('rox-test');
			$ds->execute("DROP TABLE `users`");
		} catch(Exception $e) {
		}

		$sql = "CREATE TABLE `users` (";
		$sql.= "`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,";
		$sql.= "`first_name` VARCHAR( 255 ) NOT NULL ,";
		$sql.= "`last_name` VARCHAR( 255 ) NOT NULL ,";
		$sql.= "`email` VARCHAR( 255 ) NOT NULL ,";
		$sql.= "`password` VARCHAR( 255 ) NOT NULL ,";
		$sql.= "`role` VARCHAR( 255 ) NULL ,";
		$sql.= "PRIMARY KEY ( `id` )";
		$sql.= ") ENGINE = InnoDB";

		$ds = Rox_ConnectionManager::getDataSource('rox-test');
		$ds->execute($sql);
	}

	public function tearDown() {
		$ds = Rox_ConnectionManager::getDataSource('rox-test');
		$ds->execute("DROP TABLE `users`");
	}

	public function testConstructor() {
		$user = new User(array(
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'jdoe@example.net'
		));

		$this->assertEquals('John', $user->first_name);
		$this->assertEquals('Doe', $user->last_name);
		$this->assertEquals('jdoe@example.net', $user->email);
	}

	public function testSaveRead() {
		$user = new User;
		$user->first_name = 'John';
		$user->last_name = 'Doe';
		$user->email = 'jdoe@example.net';
		$user->password = 'pass';
		$user->role = 'Admin';

		$this->assertTrue($user->save());
		$this->assertTrue(is_numeric($user->id));

		$user2 = User::find($user->id);
		$this->assertEquals($user->getData(), $user2->getData());
	}

	public function testFindAll() {
		for ($i=0; $i<2; $i++) {
			$user = new User(array(
				'first_name' => uniqid(),
				'last_name' => uniqid(),
				'email' => uniqid() . '@example.net',
				'password' => 'pass'
			));
			$user->save();
		}

		$users = User::findAll();
		$this->assertEquals(2, count($users));
		$this->assertTrue($users[0] instanceof User);
		//print_r($users);
	}

	public function testCreate() {
		$jose = User::create(array(
			'first_name' => 'Jose',
			'last_name'  => 'Perez',
			'email'      => 'jose@example.com',
			'password'   => 'test'
		));

		$this->assertTrue($jose instanceof User);
		$this->assertTrue(is_numeric($jose->getId()));
	}

	public function testFindBy() {
		User::create(array(
			'first_name' => 'Jose',
			'last_name'  => 'Perez',
			'email'      => 'jose@example.com',
			'password'   => 'test'
		));

		$jose = User::findByEmail('jose@example.com');
		$this->assertTrue($jose instanceof User);
	}

	public function testProtectedFields() {
		$user = new User(array(
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
		$user = new User;
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
		$user = new User;
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
*/

<?php

require_once dirname(dirname(__FILE__)) . '/helper.php';

Rox_ConnectionManager::setConfig('rox-test', array(
	'database' => 'rox_test'
));

class User extends Rox_ActiveRecord {

	protected $_dataSourceName = 'rox-test';

	protected $_protectedAttributes = array('role');

	public static function model($class = __CLASS__) {
		return parent::model($class);
	}

	protected function _beforeSave() {
		if ($this->_newRecord) {
			$this->password = md5($this->password);
		}
	}

	public function buildConditionsSQL($conditions) {
		return $this->_buildConditionsSQL($conditions);
	}
}

class ActiveRecordTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
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

		for ($i=0; $i<20; $i++) {
			$user = new User(array(
				'first_name' => uniqid(),
				'last_name' => uniqid(),
				'email' => uniqid() . '@example.net',
				'password' => 'pass'
			));
			$user->save();
		}
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

		$user2 = User::model()->find($user->id);
		$this->assertEquals($user->getData(), $user2->getData());
	}

	public function testFindAll() {
		$users = User::model()->findAll();
		$this->assertEquals(20, count($users));
		$this->assertTrue($users[0] instanceof User);
		//print_r($users);
	}

	public function testCreate() {
		$jose = User::model()->create(array(
			'first_name' => 'Jose',
			'last_name'  => 'Perez',
			'email'      => 'jose@example.com',
			'password'   => 'test'
		));

		$this->assertTrue($jose instanceof User);
		$this->assertTrue(is_numeric($jose->getId()));
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
		$result = $user->buildConditionsSQL(array('first_name' => 'John'));
		$expected = " WHERE `first_name` = 'John'";
		$this->assertEquals($expected, $result);

		$result = $user->buildConditionsSQL(array('first_name' => 'John', 'role' => 'Admin'));
		$expected = " WHERE `first_name` = 'John' AND `role` = 'Admin'";
		$this->assertEquals($expected, $result);

		$result = $user->buildConditionsSQL('`id` = 40');
		$expected = " WHERE `id` = 40";
		$this->assertEquals($expected, $result);
	}
}

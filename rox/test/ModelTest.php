<?php

error_reporting(E_ALL | E_STRICT);

require '../Object.php';
require '../Constants.php';
require '../Model.php';
require '../ConnectionManager.php';
require '../Datasource.php';

ConnectionManager::setConfig('default', array(
	'database' => 'rox_test'
));

class User extends Model {
	protected $_table = 'users';
	protected $_fieldMap = array(
		'id' => DATATYPE_INTEGER,
		'first_name' => DATATYPE_STRING,
		'last_name' => DATATYPE_STRING,
		'email' => DATATYPE_STRING,
		'password' => DATATYPE_STRING,
		'role' => DATATYPE_STRING
	);

	protected $_protectedFields = array('role');

	/**
	 * User::_beforeSave()
	 *
	 * @return void
	 */
	protected function _beforeSave() {
		if ($this->_newRecord) {
			$password = $this->getData('password');
			$this->setData('password', md5($password));	
		}
	}
}

class ModelTest extends PHPUnit_Framework_TestCase {

	/**
	 * ModelTest::setUp()
	 *
	 * @return void
	 */
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

		$ds = ConnectionManager::getDataSource();
		$ds->execute($sql);
	}

	/**
	 * ModelTest::tearDown()
	 *
	 * @return void
	 */
	public function tearDown() {
		$sql = "DROP TABLE `users`";
		$ds = ConnectionManager::getDataSource();
		$ds->execute($sql);
	}

	/**
	 * ModelTest::testConstructor()
	 *
	 * @return void
	 */
	public function testConstructor() {
		$user = new User(array(
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'jdoe@example.net'
		));

		$this->assertEquals('John', $user->getData('first_name'));
		$this->assertEquals('Doe', $user->getData('last_name'));
		$this->assertEquals('jdoe@example.net', $user->getData('email'));
		$this->assertNull($user->getData('password'));
	}

	/**
	 * ModelTest::testSaveRead()
	 *
	 * @return void
	 */
	public function testSaveRead() {
		$data = array(
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'jdoe@example.net',
			'password' => 'pass'
		);

		$user = new User($data);
		$saved = $user->save();
		//print_r($user);

		$this->assertTrue($saved);
		$this->assertTrue(is_numeric($user->getId()));

		$userId = $user->getId();
		$user2 = new User;
		$user2->read($userId);
		$this->assertEquals($user->getData(), $user2->getData());
	}

	/**
	 * ModelTest::testFindAll()
	 *
	 * @return void
	 */
	public function testFindAll() {
		for ($i=0; $i<20; $i++) {
			$data = array(
				'first_name' => uniqid(),
				'last_name' => uniqid(),
				'email' => uniqid() . '@example.net',
				'password' => 'pass'
			);
			$user = new User($data);
			$user->save();
		}

		$users = $user->findAll();
		$this->assertEquals(20, count($users));
		$this->assertTrue($users[0] instanceof User);
		//print_r($users);
	}

	/**
	 * ModelTest::testCreate()
	 *
	 * @return void
	 */
	public function testCreate() {
		$user = new User;
		$jose = $user->create(array(
			'first_name' => 'Jose',
			'last_name'  => 'Perez',
			'email'      => 'jose@example.com'
		));
		$this->assertTrue(is_numeric($jose->getId()));
	}

	/**
	 * ModelTest::testProtectedFields()
	 * 
	 * @return void
	 */
	public function testProtectedFields() {
		$data = array(
			'first_name' => 'Jane',
			'last_name'  => 'Doe',
			'email'      => 'jane@example.com',
			'role'       => 'Admin'
		);

		$user = new User($data);
		$this->assertEquals(null, $user->getData('role'));

		$user->setData('role', 'Admin');
		$this->assertEquals('Admin', $user->getData('role'));
	}
}

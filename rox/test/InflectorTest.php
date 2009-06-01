<?php

require_once '../Inflector.php';

class InflectorTest extends PHPUnit_Framework_TestCase {

	/**
	 * InflectorTest::testPluralize()
	 * 
	 * @return void
	 */
	public function testPluralize() {
		$result = Rox_Inflector::pluralize('client');
		$this->assertEquals('clients', $result);

		$result = Rox_Inflector::pluralize('Tomato');
		$this->assertEquals('Tomatoes', $result);

		$result = Rox_Inflector::pluralize('Metal Box');
		$this->assertEquals('Metal Boxes', $result);

		$result = Rox_Inflector::pluralize('fish');
		$this->assertEquals('fish', $result);

		$result = Rox_Inflector::pluralize('Database');
		$this->assertEquals('Databases', $result);
	}

	/**
	 * InflectorTest::testPluralize()
	 * 
	 * @return void
	 */
	public function testSingularize() {
		$result = Rox_Inflector::singularize('presidents');
		$this->assertEquals('president', $result);

		$result = Rox_Inflector::singularize('tomatoes');
		$this->assertEquals('tomato', $result);
	}

	/**
	 * InflectorTest::testUnderscore()
	 * 
	 * @return void
	 */
	public function testUnderscore() {
		$result  = Rox_Inflector::underscore('BankAccount');
		$this->assertEquals('bank_account', $result);

		$result  = Rox_Inflector::underscore('PHPDeveloper');
		$this->assertEquals('php_developer', $result);

		$result  = Rox_Inflector::underscore('ICountable');
		$this->assertEquals('i_countable', $result);
	}

	/**
	 * InflectorTest::testTableize()
	 * 
	 * @return void
	 */
	public function testTableize() {
		$result  = Rox_Inflector::tableize('Post');
		$this->assertEquals('posts', $result);

		$result  = Rox_Inflector::tableize('PageCategory');
		$this->assertEquals('page_categories', $result);

		$result  = Rox_Inflector::tableize('App_Model_Page');
		$this->assertEquals('pages', $result);

		$result  = Rox_Inflector::tableize('App_Forum_Model_Post');
		$this->assertEquals('forum_posts', $result);
	}
}

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

use \rox\Inflector;

/**
 * Test case for \rox\Inflector
 *
 * @package \rox\test
 */
class InflectorTest extends \PHPUnit_Framework_TestCase {

	public function testPluralize() {
		$result = Inflector::pluralize('client');
		$this->assertEquals('clients', $result);

		$result = Inflector::pluralize('Tomato');
		$this->assertEquals('Tomatoes', $result);

		$result = Inflector::pluralize('Metal Box');
		$this->assertEquals('Metal Boxes', $result);

		$result = Inflector::pluralize('fish');
		$this->assertEquals('fish', $result);

		$result = Inflector::pluralize('Database');
		$this->assertEquals('Databases', $result);
	}

	public function testSingularize() {
		$result = Inflector::singularize('presidents');
		$this->assertEquals('president', $result);

		$result = Inflector::singularize('tomatoes');
		$this->assertEquals('tomato', $result);

		$result = Inflector::singularize('equipment');
		$this->assertEquals('equipment', $result);	
	}

	public function testUnderscore() {
		$result  = Inflector::underscore('BankAccount');
		$this->assertEquals('bank_account', $result);

		$result  = Inflector::underscore('PHPDeveloper');
		$this->assertEquals('php_developer', $result);

		$result  = Inflector::underscore('ICountable');
		$this->assertEquals('i_countable', $result);
	}

	public function testTableize() {
		$result = Inflector::tableize('Post');
		$this->assertEquals('posts', $result);

		$result = Inflector::tableize('PageCategory');
		$this->assertEquals('page_categories', $result);

		$result = Inflector::tableize('App_Model_Page');
		$this->assertEquals('pages', $result);

		$result = Inflector::tableize('App_Forum_Model_Post');
		$this->assertEquals('forum_posts', $result);
	}

	public function testClassify() {
		$result = Inflector::classify('user_accounts');
		$this->assertEquals('UserAccount', $result);
	}
}

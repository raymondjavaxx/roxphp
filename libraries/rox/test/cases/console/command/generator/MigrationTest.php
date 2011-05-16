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

namespace rox\test\cases\console\command\generator;

use \rox\console\command\generator\Migration;

/**
 * Test case for \rox\console\command\generator\Migration
 *
 * @package \rox\test
 */
class MigrationTest extends \PHPUnit_Framework_TestCase {

	public function testInferMigrationType() {
		$result = Migration::inferMigrationType('create_articles');
		$this->assertEquals('create_table', $result);

		$result = Migration::inferMigrationType('add_slug_to_articles');
		$this->assertEquals('add_columns', $result);

		$result = Migration::inferMigrationType('create_indexes_on_articles_slugs');
		$this->assertEquals('other', $result);
	}

	public function testInferTableName() {
		$result = Migration::inferTableName('create_articles');
		$this->assertEquals('articles', $result);

		$result = Migration::inferTableName('create_articles_table');
		$this->assertEquals('articles', $result);

		$result = Migration::inferTableName('add_slug_to_articles');
		$this->assertEquals('articles', $result);

		$result = Migration::inferTableName('add_slug_to_articles_table');
		$this->assertEquals('articles', $result);

		$result = Migration::inferTableName('add_acl_fields_to_articles');
		$this->assertEquals('articles', $result);
	}

	public function testParseColumnDefinitions() {
		$defs = array('title', 'slug', 'comments_count:integer', 'published:boolean');

		$expected = array(
			array('name' => 'title', 'type' => 'string'),
			array('name' => 'slug', 'type' => 'string'),
			array('name' => 'comments_count', 'type' => 'integer'),
			array('name' => 'published', 'type' => 'boolean')
		);

		$result = Migration::parseColumnDefinitions($defs);
		$this->assertEquals($expected, $result);
	}

	public function testExtractIndexes() {
		$columns = array(
			array('name' => 'title', 'type' => 'string'),
			array('name' => 'slug', 'type' => 'string'),
			array('name' => 'site_id', 'type' => 'integer'),
			array('name' => 'user_id', 'type' => 'integer')
		);

		$expected = array('site_id', 'user_id');

		$result = Migration::extractIndexes($columns);
		$this->assertEquals($expected, $result);
	}
}

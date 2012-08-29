<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2012 Ramon Torres
 * @package \rox\test
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\test\cases\template\helper;

use \rox\template\helper\Html;
use \rox\active_record\PaginationResult;

/**
 * Test case for HTML Helper
 *
 * @package \rox\test
 */
class HtmlHelperTest extends \PHPUnit_Framework_TestCase {

	public $htmlHelper;

	public function setUp() {
		$this->htmlHelper = new Html;
	}

	public function testJavascript() {
		$result = $this->htmlHelper->javascript('test');
		$matcher = array('tag' => 'script');
		$this->assertTag($matcher, $result);

		$result = $this->htmlHelper->javascript('test', array('defer' => true));
		$matcher = array('tag' => 'script', 'attributes' => array('defer' => 'defer'));
		$this->assertTag($matcher, $result);

		$result = $this->htmlHelper->javascript('test', array('defer' => true, 'async' => true));
		$matcher = array('tag' => 'script', 'attributes' => array('defer' => 'defer', 'async' => 'async'));
		$this->assertTag($matcher, $result);
	}
}

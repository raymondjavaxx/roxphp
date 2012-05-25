<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2012 Ramon Torres
 * @package \rox\test
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\test\cases\util;

use \rox\util\Environment;

/**
 * Test case for \rox\util\Environment
 *
 * @package \rox\test
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase {

	public function testDetect() {
		$env = Environment::detect();
		$this->assertEquals('development', $env);

		putenv("PHP_ROXPHP_ENV=production");
		$env = Environment::detect();
		$this->assertEquals('production', $env);
	}
}

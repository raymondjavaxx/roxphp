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
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\console;

/**
 * undocumented class
 *
 * @package default
 */
abstract class Command {

	public function header() {
		$this->hr();
		$this->out(' RoxPHP Console');
		$this->hr();
	}

	public function run($argc, $argv) {
	}

	public function hr() {
		return $this->out(str_repeat('-', 50));
	}

	public function out($data) {
		return fwrite(STDOUT, $data . "\n");
	}

	public function in() {
		return rtrim(fgets(STDIN));
	}

	public function error($data) {
		return fwrite(STDERR, $data . "\n");
	}
}

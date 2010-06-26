<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * undocumented class
 *
 * @package default
 */
abstract class Rox_Console_Command {

	public $stdin;
	public $stdout;
	public $stderr;

	public function __construct() {
		$this->stdin  = fopen('php://stdin', 'r');
		$this->stdout = fopen('php://stdout', 'w');
		$this->stderr = fopen('php://stderr', 'w');
	}

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
		return fwrite($this->stdout, $data . "\n");
	}

	public function in() {
		return rtrim(fgets($this->stdin));
	}

	public function error($data) {
		return fwrite($this->stderr, $data . "\n");
	}
}

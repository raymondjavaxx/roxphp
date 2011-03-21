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
class Rox_Console_Command_Db extends Rox_Console_Command {

	public function header() {
		$this->hr();
		$this->out(' RoxPHP Database Migrations');
		$this->hr();
	}

	public function run($argc, $argv) {
		if ($argc < 3) {
			return $this->out('usage: rox db migrate <[up|down]>');
		}

		switch ($argv[2]) {
			case 'migrate':
				$migrator = new Rox_ActiveRecord_Migration_Migrator;
				$direction = isset($argv[3]) ? $argv[3] : 'up';
				$migrator->migrate($direction);
				break;
			
			default:
				$this->error('Invalid command');
				break;
		}
	}
}

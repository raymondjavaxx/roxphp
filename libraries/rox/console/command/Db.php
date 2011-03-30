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

namespace rox\console\command;

use \rox\active_record\migration\Migrator;

/**
 * undocumented class
 *
 * @package default
 */
class Db extends \rox\console\Command {

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
				$migrator = new Migrator;
				$direction = isset($argv[3]) ? $argv[3] : 'up';
				$migrator->migrate($direction);
				break;
			
			default:
				$this->error('Invalid command');
				break;
		}
	}
}

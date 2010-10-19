<?php

class Rox_Console_Command_Db extends Rox_Console_Command {

	public function header() {
		$this->hr();
		$this->out(' RoxPHP Database Migrations');
		$this->hr();
	}

	public function run($argc, $argv) {
		switch ($argv[2]) {
			case 'migrate':
				$migrator = new Rox_ActiveRecord_Migration_Migrator;
				$migrator->migrate($argv[3]);
				break;
			
			default:
				# code...
				break;
		}
	}
}

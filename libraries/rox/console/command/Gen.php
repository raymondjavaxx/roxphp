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

use \rox\Exception;

/**
 * undocumented class
 *
 * @package default
 */
class Gen extends \rox\console\Command {

	public function header() {
		$this->hr();
		$this->out(' RoxPHP Generator');
		$this->hr();
	}

	public function run($argc, $argv) {
		switch ($argv[2]) {
			case 'scaffold':
				$generator = new generator\Scaffold($this);
				$generator->generate($argv[3], array_slice($argv, 4));
				break;

			case 'controller':
				$generator = new generator\Controller($this);
				$generator->generate($argv[3]);
				break;

			case 'model':
				$generator = new generator\Model($this);
				$generator->generate($argv[3]);
				break;

			case 'views':
				$generator = new generator\Views($this);
				$generator->generate($argv[3]);
				break;

			case 'migration':
				$generator = new generator\Migration($this);
				$generator->generate($argv[3], array_slice($argv, 4));
				break;
		}
	}
}

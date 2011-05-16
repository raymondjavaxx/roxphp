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

namespace rox\console\command\generator;

use \rox\Inflector;
use \rox\Exception;

/**
 * Scaffold Generator
 *
 * @package default
 */
class Scaffold extends Generator {

	public function generate($name, $colDefs = array()) {
		if (empty($colDefs)) {
			throw new Exception('Scaffold generator requires a list of columns/attributes');
		}

		$name = Inflector::pluralize($name);

		$migrationGen = new Migration($this->command);
		$migrationGen->generate("create_{$name}", $colDefs);

		$modelGen = new Model($this->command);
		$modelGen->generate($name);

		$controllerGen = new Controller($this->command);
		$controllerGen->generate($name);

		$viewsGen = new Views($this->command);
		$viewsGen->generate($name, $colDefs);
	}
}

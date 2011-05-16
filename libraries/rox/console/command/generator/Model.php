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

/**
 * Model Generator
 *
 * @package default
 */
class Model extends Generator {

	public function generate($name) {
		$vars = array(
			'class_name' => Inflector::classify($name),
			'friendly_model_name' => Inflector::humanize(Inflector::classify($name)),
			'package_name' => 'App',
			'year' => date('Y')
		);

		$data = $this->_renderTemplate('model', $vars);
		$this->_writeFile('/models/' . Inflector::classify($name) . '.php', $data);
	}
}

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
 * Controller Generator
 *
 * @package default
 */
class Controller extends Generator {

	public function generate($name) {
		$vars = array(
			'controller_name'     => Inflector::tableize($name),
			'controller_class'    => Inflector::camelize(Inflector::tableize($name) . '_controller'),
			'model_name'          => Inflector::underscore(Inflector::singularize($name)),
			'model_class'         => Inflector::classify($name),
			'model_var_name'      => Inflector::lowerCamelize(Inflector::classify($name)),
			'model_var_plural_name' => Inflector::lowerCamelize(Inflector::tableize($name)),
			'friendly_model_name' => Inflector::humanize(Inflector::singularize($name)),
			'friendly_controller_name' => Inflector::humanize(Inflector::tableize($name)),
			'package_name'        => 'App',
			'year'                => date('Y')
		);

		$data = $this->_renderTemplate('controller', $vars);
		$this->_writeFile('/controllers/' . $vars['controller_class'] . '.php', $data);
	}
}

<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package App
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2010 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

class Rox_Console_Command_Gen extends Rox_Console_Command {

	public function header() {
		$this->hr();
		$this->out(' RoxPHP Generator');
		$this->hr();
	}

	public function run($argc, $argv) {
		/*
		$vars = array(
			'class_name' => Rox_Inflector::classify($argv[3]),
			'friendly_model_name' => Rox_Inflector::humanize($argv[3]),
			'package_name' => 'App',
			'year' => date('Y')
		);

		$data = $this->_renderTemplate('model', $vars);
		file_put_contents(ROX_APP_PATH . '/models/' . Rox_Inflector::classify($argv[3]) . '.php', $data);
		*/

		$vars = array(
			'controller_name'     => Rox_Inflector::tableize($argv[3]),
			'controller_class'    => Rox_Inflector::camelize(Rox_Inflector::tableize($argv[3]) . '_controller'),
			'model_name'          => Rox_Inflector::underscore(Rox_Inflector::singularize($argv[3])),
			'model_class'         => Rox_Inflector::classify($argv[3]),
			'model_var_name'      => Rox_Inflector::lowerCamelize(Rox_Inflector::classify($argv[3])),
			'model_var_plural_name' => Rox_Inflector::lowerCamelize(Rox_Inflector::tableize($argv[3])),
			'friendly_model_name' => Rox_Inflector::humanize($argv[3]),
			'friendly_controller_name' => Rox_Inflector::humanize(Rox_Inflector::tableize($argv[3])),
			'package_name'        => 'App',
			'year'                => date('Y')
		);

		$data = $this->_renderTemplate('controller', $vars);
		file_put_contents(ROX_APP_PATH . '/controllers/' . $vars['controller_class'] . '.php', $data);
	}

	protected function _renderTemplate($name, $vars = array()) {
		$data = file_get_contents(dirname(__FILE__) . '/templates/' . $name . '.tpl');

		foreach ($vars as $k => $v) {
			$data = str_replace('{' . $k . '}', $v, $data);
		}

		return $data;
	}
}

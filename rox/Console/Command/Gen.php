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
class Rox_Console_Command_Gen extends Rox_Console_Command {

	public function header() {
		$this->hr();
		$this->out(' RoxPHP Generator');
		$this->hr();
	}

	public function run($argc, $argv) {
		switch ($argv[2]) {
			case 'controller':
				$this->_generateController($argv[3]);
				break;

			case 'model':
				$this->_generateModel($argv[3]);
				break;

			case 'views':
				$this->_generateViews($argv[3]);
				break;

			case 'migration':
				$this->_generateMigration($argv[3]);
				break;
		}
	}

	protected function _generateModel($name) {
		$vars = array(
			'class_name' => Rox_Inflector::classify($name),
			'friendly_model_name' => Rox_Inflector::humanize(Rox_Inflector::classify($name)),
			'package_name' => 'App',
			'year' => date('Y')
		);

		$data = $this->_renderTemplate('model', $vars);
		$this->_writeFile('/models/' . Rox_Inflector::classify($name) . '.php', $data);
	}

	protected function _generateController($name) {
		$vars = array(
			'controller_name'     => Rox_Inflector::tableize($name),
			'controller_class'    => Rox_Inflector::camelize(Rox_Inflector::tableize($name) . '_controller'),
			'model_name'          => Rox_Inflector::underscore(Rox_Inflector::singularize($name)),
			'model_class'         => Rox_Inflector::classify($name),
			'model_var_name'      => Rox_Inflector::lowerCamelize(Rox_Inflector::classify($name)),
			'model_var_plural_name' => Rox_Inflector::lowerCamelize(Rox_Inflector::tableize($name)),
			'friendly_model_name' => Rox_Inflector::humanize($name),
			'friendly_controller_name' => Rox_Inflector::humanize(Rox_Inflector::tableize($name)),
			'package_name'        => 'App',
			'year'                => date('Y')
		);

		$data = $this->_renderTemplate('controller', $vars);
		$this->_writeFile('/controllers/' . $vars['controller_class'] . '.php', $data);
	}

	protected function _generateViews($name) {
		$tableName = Rox_Inflector::tableize($name);
		$datasource = Rox_ConnectionManager::getDataSource();
		$attributes = $datasource->generateAttributeMapFromTable($tableName);

		$templates = array('add', 'edit', 'index', 'view');

		$vars = array(
			'attributes' => $attributes,
			'friendlyModelName' => Rox_Inflector::humanize(Rox_Inflector::classify($name)),
			'modelVarName' => Rox_Inflector::lowerCamelize(Rox_Inflector::classify(Rox_Inflector::singularize($name))),
			'pluralModelVarName' => Rox_Inflector::lowerCamelize(Rox_Inflector::pluralize($name)),
			'controller' => Rox_Inflector::tableize($name)
		);

		foreach ($templates as $template) {	
			$data = $this->_renderTemplate("views/{$template}", $vars, true);	
			$folder = Rox_Inflector::tableize($name);
			$this->_writeFile("/views/{$folder}/{$template}.html.tpl", $data);
		}
	}

	protected function _generateMigration($name) {
		$name = Rox_Inflector::underscore($name);

		foreach (glob(ROX_APP_PATH . '/config/migrations/*.php') as $file) {
			if (preg_match("/([0-9]+)_{$name}.php/", $file) == 1) {
				throw new Exception("A migration named {$name} already exists");
			}
		}

		$version = gmdate('YmdHis');

		$data = $this->_renderTemplate('migration', array(
			'class_name' => Rox_Inflector::camelize($name),
			'year' => date('Y')
		));

		$this->_writeFile("/config/migrations/{$version}_{$name}.php", $data);
	}

	protected function _writeFile($file, $data) {
		$absolutePath = ROX_APP_PATH . $file;

		$directory = dirname($absolutePath);
		clearstatcache();
		if (!is_dir($directory)) {
			mkdir($directory, 0777, true);
		}

		if (file_exists($absolutePath)) {
			do {
				$this->out("File app/{$file} already exists. Do you want to override it?(y,N)");
				$answer = strtolower($this->in());
			} while (!in_array($answer, array('', 'y', 'n')));

			if ($answer != 'y') {
				return false;
			}
		}

		$this->out("Writing file: app{$file}");
		return file_put_contents($absolutePath, $data);
	}

	protected function _renderTemplate($name, $vars = array(), $runCode = false) {
		if ($runCode) {
			extract($vars, EXTR_SKIP);

			ob_start();
			require dirname(__FILE__) . '/templates/' . $name . '.tpl';
			$data = ob_get_clean();
		} else {
			$data = file_get_contents(dirname(__FILE__) . '/templates/' . $name . '.tpl');
			foreach ($vars as $k => $v) {
				$data = str_replace('{' . $k . '}', $v, $data);
			}
		}

		return $data;
	}
}

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

use \rox\Inflector;
use \rox\active_record\ConnectionManager;

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
			'class_name' => Inflector::classify($name),
			'friendly_model_name' => Inflector::humanize(Inflector::classify($name)),
			'package_name' => 'App',
			'year' => date('Y')
		);

		$data = $this->_renderTemplate('model', $vars);
		$this->_writeFile('/models/' . Inflector::classify($name) . '.php', $data);
	}

	protected function _generateController($name) {
		$vars = array(
			'controller_name'     => Inflector::tableize($name),
			'controller_class'    => Inflector::camelize(Inflector::tableize($name) . '_controller'),
			'model_name'          => Inflector::underscore(Inflector::singularize($name)),
			'model_class'         => Inflector::classify($name),
			'model_var_name'      => Inflector::lowerCamelize(Inflector::classify($name)),
			'model_var_plural_name' => Inflector::lowerCamelize(Inflector::tableize($name)),
			'friendly_model_name' => Inflector::humanize($name),
			'friendly_controller_name' => Inflector::humanize(Inflector::tableize($name)),
			'package_name'        => 'App',
			'year'                => date('Y')
		);

		$data = $this->_renderTemplate('controller', $vars);
		$this->_writeFile('/controllers/' . $vars['controller_class'] . '.php', $data);
	}

	protected function _generateViews($name) {
		$tableName = Inflector::tableize($name);
		$datasource = ConnectionManager::getDataSource();
		$attributes = $datasource->generateAttributeMapFromTable($tableName);

		$templates = array('add', 'edit', 'index', 'view');

		$vars = array(
			'attributes' => $attributes,
			'friendlyModelName' => Inflector::humanize(Inflector::classify($name)),
			'modelVarName' => Inflector::lowerCamelize(Inflector::classify(Inflector::singularize($name))),
			'pluralModelVarName' => Inflector::lowerCamelize(Inflector::pluralize($name)),
			'controller' => Inflector::tableize($name)
		);

		foreach ($templates as $template) {	
			$data = $this->_renderTemplate("views/{$template}", $vars, true);	
			$folder = Inflector::tableize($name);
			$this->_writeFile("/views/{$folder}/{$template}.html.tpl", $data);
		}
	}

	protected function _generateMigration($name) {
		$name = Inflector::underscore($name);

		foreach (glob(ROX_APP_PATH . '/config/migrations/*.php') as $file) {
			if (preg_match("/([0-9]+)_{$name}.php/", $file) == 1) {
				throw new Exception("A migration named {$name} already exists");
			}
		}

		$version = gmdate('YmdHis');

		$data = $this->_renderTemplate('migration', array(
			'class_name' => Inflector::camelize($name),
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

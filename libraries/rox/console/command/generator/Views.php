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
use \rox\active_record\ConnectionManager;

/**
 * Views Generator
 *
 * @package default
 */
class Views extends Generator {

	public function generate($name, $colDefs = array()) {
		if (empty($colDefs)) {
			$tableName = Inflector::tableize($name);
			$datasource = ConnectionManager::getDataSource();
			$attributes = $datasource->generateAttributeMapFromTable($tableName);
		} else {
			$columns = Migration::parseColumnDefinitions($colDefs);
			$names = array_map(function($col) { return $col['name']; }, $columns);
			$types = array_map(function($col) { return $col['type']; }, $columns);
			$attributes = array_combine($names, $types);
		}

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
}

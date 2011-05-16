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

use \rox\Exception;
use \rox\Inflector;

/**
 * Migration Generator
 *
 * @package default
 */
class Migration extends Generator {

	public function generate($name, $colDefs = array()) {
		$name = Inflector::underscore($name);
		$version = gmdate('YmdHis');

		foreach (glob(ROX_APP_PATH . '/config/migrations/*.php') as $file) {
			if (preg_match("/([0-9]+)_{$name}.php/", $file) == 1) {
				throw new Exception("A migration named {$name} already exists");
			}
		}

		$type = static::inferMigrationType($name);
		$class = Inflector::camelize($name);
		$table = static::inferTableName($name);
		$columns = static::parseColumnDefinitions($colDefs);
		$indexes = static::extractIndexes($columns);

		$vars = compact('type', 'class', 'table', 'columns', 'indexes');
		$data = $this->_renderTemplate('migration', $vars, true);

		$this->_writeFile("/config/migrations/{$version}_{$name}.php", $data);
	}

	public static function inferTableName($migrationName) {
		$table = 'your_table';

		$patterns = array(
			'/create_(?<table>[a-z_]+)_table$/',
			'/create_(?<table>[a-z_]+)$/',
			'/(.*)_to_(?<table>[a-z_]+)_table$/',
			'/(.*)_to_(?<table>[a-z_]+)$/',
		);

		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $migrationName, $matches) == 1 && isset($matches['table'])) {
				$table = $matches['table'];
				break;
			}
		}

		return $table;
	}

	public static function inferMigrationType($migrationName) {
		$patterns = array(
			'/create_(.*)_on_(.*)$/' => 'other',
			'/add_(.*)_indexes_to_(.*)$/' => 'other',
			'/create_(?<table>[a-z_]+)_table$/' => 'create_table',
			'/create_(?<table>[a-z_]+)$/' => 'create_table',
			'/add_(.*)_to_(?<table>[a-z_]+)_table$/' => 'add_columns',
			'/add_(.*)_to_(?<table>[a-z_]+)$/' => 'add_columns'
		);

		foreach ($patterns as $pattern => $type) {
			if (preg_match($pattern, $migrationName, $matches) === 1) {
				return $type;
			}
		}

		return 'other';
	}

	public static function parseColumnDefinitions($colDefs) {
		$columns = array_map(function($def){
			if (strpos($def, ':') !== false) {
				list($name, $type) = explode(':', $def);
				return compact('name', 'type');
			}

			return array('name' => $def, 'type' => 'string');
		}, $colDefs);

		return $columns;
	}

	public static function extractIndexes($columns) {
		$indexes = array();
		foreach ($columns as $column) {
			if (strpos($column['name'], '_id') !== false) {
				$indexes[] = $column['name'];
			}
		}

		return $indexes;
	}
}

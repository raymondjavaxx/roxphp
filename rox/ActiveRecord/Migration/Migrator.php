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
 * Migrator class
 *
 * @package Rox
 */
class Rox_ActiveRecord_Migration_Migrator {

	public function migrate($direction) {
		$this->_scan($direction);

		switch ($direction) {
			case 'up':
				$this->migrateUp();
				break;

			case 'down':
				$this->migrateDown();
				break;

			default:
				throw new Rox_Exception("Invalid direction '{$direction}'");
				break;
		}
	}

	public function migrateUp() {
		$migrations = self::_pendingMigrations();
		foreach ($migrations as $migration) {
			$this->_run($migration, 'up');
		}
	}

	public function migrateDown() {
		$migration = self::_lastMigrated();
		if ($migration) {
			$this->_run($migration, 'down');
		}
	}

	protected function _run($migration, $direction) {
		echo "{$migration['class']}::{$direction}\n";
		require_once $migration['file'];
		$instance = new $migration['class'];
		call_user_func(array($instance, $direction));
		self::_flagAsMigrated($migration['version'], $direction);
	}

	protected static function _scan() {
		$migrations = array();

		$files = glob(ROX_APP_PATH . '/config/migrations/*.php');
		foreach ($files as $file) {
			if (preg_match('/(?<version>[0-9]+)_(?<name>.*).php/', $file, $matches) == 1) {
				$migrations[$matches['version']] = array(
					'file'    => $file,
					'name'    => $matches['name'],
					'class'   => Rox_Inflector::camelize($matches['name']),
					'version' => $matches['version']
				);
			}
		}

		ksort($migrations);
		return $migrations;
	}

	protected static function _pendingMigrations() {
		$migrated = self::_migratedVersions();

		$migrations = array();
		foreach (self::_scan() as $version => $migration) {
			if (!in_array($version, $migrated)) {
				$migrations[$version] = $migration;
			}
		}

		return $migrations;
	}

	public static function _lastMigrated() {
		$versions = self::_migratedVersions();
		if (empty($versions)) {
			return false;
		}

		$version = end($versions);
		$migrations = self::_scan();
		return $migrations[$version];
	}

	protected static function _migratedVersions() {
		$datasource = Rox_ConnectionManager::getDataSource();
		$rows = $datasource->query("SELECT * FROM `schema_migrations`");

		$versions = array();
		foreach ($rows as $row) {
			$versions[] = $row['version'];
		}

		sort($versions);
		return $versions;
	}

	protected static function _flagAsMigrated($version, $direction) {
		$datasource = Rox_ConnectionManager::getDataSource();
		if ($direction == 'up') {
			$sql = "INSERT INTO `schema_migrations`(`version`) VALUES('{$version}')";
			$datasource->execute($sql);
		} else {
			$sql = "DELETE FROM `schema_migrations` WHERE `version` = '{$version}'";
			$datasource->execute($sql);			
		}
	}
}

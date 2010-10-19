<?php
class Rox_ActiveRecord_Migration_Migrator {

	public $migrations = array();

	public function migrate($direction) {
		$this->_scan($direction);

		switch ($direction) {
			case 'up':
				foreach ($this->migrations as $migrationInfo) {
					require_once $migrationInfo['file'];
					$migration = new $migrationInfo['class'];
					echo "{$migrationInfo['class']}::up\n";
					$migration->up();
					$this->_flagAsMigrated($migrationInfo['version'], 'up');
				}
				break;

			case 'down':
				foreach (array_reverse($this->migrations) as $migrationInfo) {
					require_once $migrationInfo['file'];
					$migration = new $migrationInfo['class'];
					echo "{$migrationInfo['class']}::down\n";
					$migration->down();
					$this->_flagAsMigrated($migrationInfo['version'], 'down');
				}
				break;

			default:
				# code...
				break;
		}
	}

	protected function _alreadyMigrated() {
		$datasource = Rox_ConnectionManager::getDataSource();
		$rows = $datasource->query("SELECT * FROM `schema_migrations`");

		$versions = array();
		foreach ($rows as $row) {
			$versions[] = $row['version'];
		}

		return $versions;
	}

	protected function _scan($direction) {
		clearstatcache();

		$exclude = $this->_alreadyMigrated();

		$files = glob(ROX_APP_PATH . '/config/migrations/*.php');
		foreach ($files as $file) {
			if (preg_match('/(?<version>[0-9]+)_(?<file>.*).php/', $file, $matches) == 1) {
				if ($direction == 'up' && in_array($matches['version'], $exclude)) {
					continue;
				}

				$this->migrations[$matches['version']] = array(
					'file'      => $file,
					'class'     => Rox_Inflector::camelize($matches['file']),
					'version' => $matches['version']
				);
			}
		}

		ksort($this->migrations);
	}

	protected function _flagAsMigrated($version, $direction) {
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

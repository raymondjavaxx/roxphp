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

/**
 * Abstract Generator
 *
 * @package default
 */
abstract class Generator {

	public $command;

	public function __construct(\rox\console\Command $command) {
		$this->command = $command;
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
				$this->command->out("File app/{$file} already exists. Do you want to override it?(y,N)");
				$answer = strtolower($this->command->in());
			} while (!in_array($answer, array('', 'y', 'n')));
	
			if ($answer != 'y') {
				return false;
			}
		}
	
		$this->command->out("Writing file: app{$file}");
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

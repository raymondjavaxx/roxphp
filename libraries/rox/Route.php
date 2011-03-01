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

namespace rox;

use \rox\http\Request;

/**
 * Route
 *
 * @package Rox
 */
class Route {

	public $pattern;
	public $config = array();

	public function __construct($config) {
		$this->config = $config;
	}

	public function compile() {
		$template = str_replace(array('/', '.'), array('\/', '\.'), $this->config['template']);
		$this->pattern = "/^{$template}(\.(?P<extension>[a-z0-9]{1,32}))?$/";

		$replacements = array(
			'/\\:controller/' => '(?P<controller>[a-z][a-z0-9_]{0,64})',
			'/\\:action/' => '(?P<action>[a-z][a-z0-9_]{0,64})',
			'/\\:([a-z0-9_]+)/' => '(?P<\\1>[^\/.]+)'
		);

		$this->pattern = preg_replace(array_keys($replacements), array_values($replacements), $this->pattern);
	}

	public function match($path, Request $request = null) {
		if ($this->pattern === null) {
			$this->compile();
		}

		if (isset($this->config['options']['via'])
			&& $request !== null
			&& $this->config['options']['via'] != $request->getMethod()) {
			return false;
		}

		if (preg_match($this->pattern, $path, $matches) === 0) {
			return false;
		}

		preg_match_all('/:([a-z0-9_]+)/', $this->config['template'], $m);
		$sections = isset($m[1]) ? $m[1] : array();
		$sections[] = 'extension';
		$matches = array_intersect_key($matches, array_flip($sections));

		$specialSections = array('controller', 'action', 'extension');
		foreach ($specialSections as $key) {
			if (isset($matches[$key])) {
				$this->config['params'][$key] = $matches[$key];
				unset($matches[$key]);
			}
		}

		return array_merge($this->config['params'], array('args' => $matches));
	}
}

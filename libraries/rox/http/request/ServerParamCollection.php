<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2012 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2012 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\http\request;

class ServerParamCollection extends ParamCollection {

	public function getHeaders() {
		$headers = array();

		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'HTTP_') === 0) {
				$header = str_replace('_', '-', strtolower(substr($key, 5)));
				$headers[$header] = $value;
			}
		}

		return $headers;
	}
}

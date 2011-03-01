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

namespace rox\http\request\decoder;

use \rox\Exception;

/**
 * JSON Decoder
 *
 * @package Rox
 */
class Json {

	public function decode($data) {
		$result = json_decode($data, true);
		if ($result === null) {
			throw new Exception("Data is not valid JSON");
		}

		return $result;
	}
}

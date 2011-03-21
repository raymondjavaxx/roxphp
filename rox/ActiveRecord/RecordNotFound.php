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
 * RecordNotFound Exception
 *
 * @package Rox
 */
class Rox_ActiveRecord_RecordNotFound extends Rox_Exception {

	public function __construct($message = 'Record not found') {
		parent::__construct($message, 404);
	}
}

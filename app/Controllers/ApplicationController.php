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
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace App\Controllers;

/**
 * Application Controller
 *
 * @package App
 */
abstract class ApplicationController extends \Rox\Action\Controller
{
	/**
	 * Commonly used helpers
	 *
	 * @var array
	 */
	public $helpers = ['Form', 'Html', 'Pagination'];

	/**
	 * Called before the action gets invoked
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		# code...
	}
}

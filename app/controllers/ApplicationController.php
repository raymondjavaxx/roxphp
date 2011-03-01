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

/**
 * ApplicationController
 *
 * @package App
 */
abstract class ApplicationController extends \rox\Controller {

	/**
	 * Commonly used helpers
	 *
	 * @var array
	 */
	public $helpers = array('Form', 'Html', 'Pagination');
}

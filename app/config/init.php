<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Load and init the Cache class
 */
require_once(ROX . 'Cache.php');

Cache::init(Cache::ADAPTER_FILE, array(
	'cache_dir' => APP . 'tmp' . DS . 'cache' . DS
));

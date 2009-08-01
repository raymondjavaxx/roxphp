<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2009 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package Rox
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

session_name('ROXAPP');
session_start();

/**
 * Load and init the Cache class
 */
require_once(ROX . 'Cache.php');

Rox_Cache::init(Rox_Cache::ADAPTER_FILE);

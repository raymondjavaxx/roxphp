<?php
/**
 * RoxPHP  
 *  
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package	rox
 * @author Ramon Torres
 * @copyright Copyright (c) 2008 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @link http://roxphp.com 
 * @access public
 */

/**
 * Load and init the Cache class
 */
require_once(ROX . 'cache.php');

Cache::init(Cache::ADAPTER_FILE, array(
	'cache_dir' => APP . 'tmp' . DS . 'cache' . DS
));
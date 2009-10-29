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
 * @copyright Copyright (C) 2008 - 2009 Ramon Torres (http://roxphp.com)
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

define('DS', DIRECTORY_SEPARATOR);

define('ROX_APP_PATH', dirname(dirname(__FILE__)));
define('ROX_FRAMEWORK_PATH', dirname(ROX_APP_PATH).DS.'rox');

set_include_path(implode(PATH_SEPARATOR, array(
	ROX_APP_PATH.DS.'models',
	ROX_APP_PATH.DS.'controllers',
	ROX_APP_PATH.DS.'helpers',
	ROX_APP_PATH.DS.'mailer',
	ROX_FRAMEWORK_PATH,
	// uncoment the next line to preserve default include paths
	// get_include_path()
)));

// load main config file
require ROX_APP_PATH.'/config/main.php';

// set error reporting level
error_reporting(ROX_DEBUG ? E_ALL | E_STRICT : 0);

// load and set the exception handler
require ROX_FRAMEWORK_PATH.'/Exception/Handler.php';
set_exception_handler(array('Rox_Exception_Handler', 'handle'));

// load and register the autoloader
require ROX_FRAMEWORK_PATH.'/Loader.php';
Rox_Loader::register();

// load database configuration file and bootstrap
require ROX_APP_PATH.'/config/database.php';
require ROX_APP_PATH.'/config/init.php';

$dispatcher = new Rox_Dispatcher;
$dispatcher->dispatch(isset($_GET['route']) ? $_GET['route'] : ROX_DEFAULT_ROUTE);

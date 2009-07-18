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

// define paths
define('WEBROOT', dirname(__FILE__) . DS);
define('APP', dirname(WEBROOT) . DS);
define('ROX', dirname(APP) . DS . 'rox' . DS);

define('CONFIG', APP.'config'.DS);
define('MODELS', APP.'models'.DS);
define('VIEWS', APP.'views'.DS);
define('LAYOUTS', VIEWS.'layouts'.DS);
define('CONTROLLERS', APP.'controllers'.DS);
define('HELPERS', APP.'helpers'.DS);
define('MAILERS', APP.'mailers'.DS);

define('WWW', dirname(dirname(dirname($_SERVER['PHP_SELF']))));

set_include_path(implode(PATH_SEPARATOR, array(
	MODELS,
	CONTROLLERS,
	HELPERS,
	MAILERS,
	ROX,
	// uncoment the next line to preserve default include paths
	// get_include_path()
)));

// load main config file
require CONFIG.'main.php';

// set error reporting level
error_reporting(ROX_DEBUG ? E_ALL | E_STRICT : 0);

// load and set the exception handler
require ROX.'Exception'.DS.'Handler.php';
set_exception_handler(array('Rox_Exception_Handler', 'handle'));

// load and register the autoloader
require ROX.'Loader.php';
Rox_Loader::register();

require ROX.'Constants.php';

// load database configuration file and bootstrap
require CONFIG.'database.php';
require CONFIG.'init.php';

$dispatcher = new Rox_Dispatcher;
$dispatcher->dispatch(isset($_GET['route']) ? $_GET['route'] : ROX_DEFAULT_ROUTE);

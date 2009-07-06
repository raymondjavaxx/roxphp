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
 * @copyright Copyright (c) 2008 - 2009 Ramon Torres (http://roxphp.com)
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

// include configuration file
require CONFIG . 'main.php';

// set error reporting level
error_reporting(ROX_DEBUG ? E_ALL | E_STRICT : 0);

// include core files
require ROX . 'Exception' . DS . 'Handler.php';
set_exception_handler(array('Rox_Exception_Handler', 'handle'));

require ROX . 'Object.php';
require ROX . 'Rox.php';
require ROX . 'Validator.php';
require ROX . 'Registry.php';
require ROX . 'Dispatcher.php';
require ROX . 'ConnectionManager.php';
require ROX . 'DataSource.php';
require ROX . 'Router.php';
require ROX . 'Inflector.php';
require ROX . 'ActiveRecord.php'; // M
require ROX . 'View.php';         // V
require ROX . 'Controller.php';   // C
require ROX . 'Model.php';
require ROX . 'ActiveRecord/PaginationResult.php';
require ROX . 'ActiveRecord/ErrorCollection.php';
require ROX . 'Constants.php';
require ROX . 'Mailer.php';

require CONTROLLERS . 'ApplicationController.php';

require CONFIG . 'database.php';
require CONFIG . 'init.php';

$dispatcher = new Rox_Dispatcher;
$dispatcher->dispatch(isset($_GET['route']) ? $_GET['route'] : ROX_DEFAULT_ROUTE);

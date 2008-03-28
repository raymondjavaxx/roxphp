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

$NOW = microtime(true);

// define paths
define('DS', DIRECTORY_SEPARATOR);
define('WEBROOT', dirname(__FILE__));
define('APP', dirname(WEBROOT));
define('CONTROLLERS', APP.DS.'controllers');
define('MODELS', APP.DS.'models');
define('VIEWS', APP.DS.'views');
define('LAYOUTS', VIEWS.DS.'layouts');
define('ROX', dirname(APP).DS.'rox');
define('CACHE', APP.DS.'tmp'.DS.'cache');

define('WWW', dirname(dirname(dirname($_SERVER['PHP_SELF']))));

// include configuration files
include(APP.DS.'config'.DS.'main.php');
include(APP.DS.'config'.DS.'database.php');

// set error reporting level
error_reporting(ROX_DEBUG ? E_ALL : 0);

// include core files
include(ROX.DS.'exception.php');
include(ROX.DS.'object.php');
include(ROX.DS.'rox.php');
include(ROX.DS.'registry.php');
include(ROX.DS.'dispatcher.php');
include(ROX.DS.'datasource.php');
include(ROX.DS.'router.php');
include(ROX.DS.'model.php');		// M
include(ROX.DS.'view.php');			// V
include(ROX.DS.'controller.php');	// C 

$DataSource = new DataSource;
$DataSource->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
Registry::addObject('DataSource', $DataSource);

$Dispatcher = new Dispatcher;
$Dispatcher->dispatch(isset($_GET['route']) ? $_GET['route'] : ROX_DEFAULT_ROUTE);

//echo '<p> Page generated in: ' . round(microtime(true) - $NOW, 3) . ' seconds</p>';
?>
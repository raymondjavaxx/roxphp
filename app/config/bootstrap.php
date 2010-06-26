<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * This Software is released under the MIT License.
 * See license.txt for more details.
 *
 * @package App
 * @author Ramon Torres
 * @copyright Copyright (C) 2008 - 2010 Ramon Torres
 * @license http://roxphp.com/static/license.html
 * @version $Id$
 */

/**
 * Determines wheter debug mode is enabled. On debug mode, errors
 * and exceptions are displayed to the user.
 */
define('ROX_DEBUG', true);

/**
 * Controller/action to call when user lands on homepage.
 */
define('ROX_DEFAULT_ROUTE', '/pages/home');

/**
 * Some class libraries still use this.
 * @todo remove
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Path to application folder
 */
define('ROX_APP_PATH', dirname(dirname(__FILE__)));

/**
 * Path to framework folder
 */
define('ROX_FRAMEWORK_PATH', dirname(ROX_APP_PATH).'/rox');

// Set include paths
set_include_path(implode(PATH_SEPARATOR, array(
	ROX_APP_PATH . '/models',
	ROX_APP_PATH . '/controllers',
	ROX_APP_PATH . '/helpers',
	ROX_APP_PATH . '/mailers',
	ROX_FRAMEWORK_PATH,
	// uncoment the line below preserve default include paths
	// get_include_path()
)));

// Set error reporting level
error_reporting(ROX_DEBUG ? E_ALL | E_STRICT : 0);

// Load and set the exception handler
require ROX_FRAMEWORK_PATH . '/Exception/Handler.php';
Rox_Exception_Handler::register();

// Load and register the autoloader
require ROX_FRAMEWORK_PATH . '/Loader.php';
Rox_Loader::register();

// Load DB configuration file and init
require ROX_APP_PATH . '/config/database.php';
require ROX_APP_PATH . '/config/init.php';
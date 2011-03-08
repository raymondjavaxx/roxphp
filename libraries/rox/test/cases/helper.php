<?php
error_reporting(E_ALL | E_STRICT);

define('ROX_FRAMEWORK_PATH', dirname(dirname(dirname(__DIR__))));

set_include_path(implode(PATH_SEPARATOR, array(
	ROX_FRAMEWORK_PATH,
	get_include_path()
)));

// Load and register the autoloader
require ROX_FRAMEWORK_PATH . '/rox/Loader.php';
\rox\Loader::register();

// Set the default timezone used by all date/time functions
date_default_timezone_set('America/New_York');

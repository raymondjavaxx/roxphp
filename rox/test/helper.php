<?php
error_reporting(E_ALL | E_STRICT);

define('ROX_FRAMEWORK_PATH', dirname(dirname(__FILE__)));

set_include_path(implode(PATH_SEPARATOR, array(
	ROX_FRAMEWORK_PATH,
	get_include_path()
)));

require_once ROX_FRAMEWORK_PATH . '/Loader.php';

Rox_Loader::register();

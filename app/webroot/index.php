<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2010 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2010 Ramon Torres
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

// include the bootstrap file
require dirname(dirname(__FILE__)) . '/config/bootstrap.php';

$dispatcher = new Rox_Http_Dispatcher;
$dispatcher->dispatch(new Rox_Http_Request);

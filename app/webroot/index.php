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

// include the bootstrap file
require dirname(dirname(__FILE__)) . '/config/bootstrap.php';

$dispatcher = new Rox_Dispatcher;
$dispatcher->dispatch(isset($_GET['route']) ? $_GET['route'] : ROX_DEFAULT_ROUTE);

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

require_once dirname(dirname(dirname(__FILE__))) . '/app/config/bootstrap.php';

$command = new Rox_Console_Command_Gen;
$command->header();
$command->run($argc, $argv);

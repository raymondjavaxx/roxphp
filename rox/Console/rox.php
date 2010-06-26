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
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once dirname(dirname(dirname(__FILE__))) . '/app/config/bootstrap.php';

$command = new Rox_Console_Command_Gen;
$command->header();
$command->run($argc, $argv);

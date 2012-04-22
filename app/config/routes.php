<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package App
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

use \rox\Router;

// Connect the homepage
Router::connectRoot(array('controller' => 'pages', 'action' => 'home'));

// Custom routes

// Default routes
Router::on('GET', '/:controller', array('action' => 'index'));
Router::on('GET', '/:controller/new', array('action' => 'add'));
Router::on('POST','/:controller', array('action' => 'add'));
Router::on('GET', '/:controller/:id', array('action' => 'view'));
Router::on('GET', '/:controller/:id/edit', array('action' => 'edit'));
Router::on('PUT', '/:controller/:id', array('action' => 'edit'));
Router::on('DELETE', '/:controller/:id', array('action' => 'delete'));

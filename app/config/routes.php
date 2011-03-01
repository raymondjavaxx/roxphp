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
Router::connect('/:controller', array('action' => 'index'), array('via' => 'GET'));
Router::connect('/:controller/new', array('action' => 'add'), array('via' => 'GET'));
Router::connect('/:controller', array('action' => 'add'), array('via' => 'POST'));
Router::connect('/:controller/:id', array('action' => 'view'), array('via' => 'GET'));
Router::connect('/:controller/:id/edit', array('action' => 'edit'), array('via' => 'GET'));
Router::connect('/:controller/:id', array('action' => 'edit'), array('via' => 'PUT'));
Router::connect('/:controller/:id', array('action' => 'delete'), array('via' => 'DELETE'));

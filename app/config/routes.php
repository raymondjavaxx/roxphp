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

// Connect the homepage
Rox_Router::connectRoot(array('controller' => 'pages', 'action' => 'home'));

// Custom routes

// Default routes
Rox_Router::connect('/:controller', array('action' => 'index'), array('via' => 'GET'));
Rox_Router::connect('/:controller/new', array('action' => 'add'), array('via' => 'GET'));
Rox_Router::connect('/:controller', array('action' => 'add'), array('via' => 'POST'));
Rox_Router::connect('/:controller/:id', array('action' => 'view'), array('via' => 'GET'));
Rox_Router::connect('/:controller/:id/edit', array('action' => 'edit'), array('via' => 'GET'));
Rox_Router::connect('/:controller/:id', array('action' => 'edit'), array('via' => 'PUT'));
Rox_Router::connect('/:controller/:id', array('action' => 'delete'), array('via' => 'DELETE'));

// Legacy routes
//Rox_Router::connect('/:controller/:action/:id', array());
//Rox_Router::connect('/:controller/:action/:id/:param1/:param2', array());
//Rox_Router::connect('/:controller/:action', array());
//Rox_Router::connect('/:controller', array('action' => 'index'));

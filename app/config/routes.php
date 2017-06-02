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
Router::connectRoot(['controller' => 'pages', 'action' => 'home']);

// Custom routes

// Router::on('GET', '/articles', ['controller' => 'articles', action' => 'index']);
// Router::on('GET', '/articles/:id', ['controller' => 'articles', action' => 'view']);

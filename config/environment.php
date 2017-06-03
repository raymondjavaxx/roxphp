<?php

use \Rox\Utils\Environment;

$environment = Environment::detect();
require_once __DIR__ . "/environments/{$environment}.php";

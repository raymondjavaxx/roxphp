<?php

use \rox\util\Environment;

$environment = Environment::detect();
require_once __DIR__ . "/environments/{$environment}.php";

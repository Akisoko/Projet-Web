<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

$env = parse_ini_file(__DIR__ . '/../.env');

define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);

$router = new Router();
$router->handle($_SERVER['REQUEST_URI']);
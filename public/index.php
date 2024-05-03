<?php

define('BASE_PATH', dirname(__DIR__));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once BASE_PATH.'/vendor/autoload.php';

use Framework\Http\Kernel;
use Framework\Http\Request;

$request = Request::createFromGlobals();

$container = include BASE_PATH.'/config/services.php';

$kernel = $container->get(Kernel::class);

$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);

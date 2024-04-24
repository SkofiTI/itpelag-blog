<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

use Framework\Http\Kernel;
use Framework\Http\Request;
use Framework\Routing\Router;

$request = Request::createFromGlobals();

$router = new Router();

$kernel = new Kernel($router);
$response = $kernel->handle($request);

$response->send();
<?php

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH.'/vendor/autoload.php';

use Framework\Http\Kernel;
use Framework\Http\Request;
use Framework\Routing\RouterInterface;

$request = Request::createFromGlobals();

$container = include BASE_PATH.'/config/services.php';

$router = $container->get(RouterInterface::class);
$kernel = $container->get(Kernel::class);

$response = $kernel->handle($request);
$response->send();
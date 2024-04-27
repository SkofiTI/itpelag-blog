<?php

use Doctrine\DBAL\Connection;
use Framework\Console\Commands\MigrateCommand;
use Framework\Controller\AbstractController;
use Framework\Dbal\ConnectionFactory;
use Framework\Routing\Router;
use Framework\Routing\RouterInterface;
use Framework\Session\Session;
use Framework\Session\SessionInterface;
use Framework\Template\TwigFactory;
use League\Container\Argument\Literal\ArrayArgument;
use League\Container\Argument\Literal\StringArgument;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH.'/.env');

$routes = include BASE_PATH.'/routes/web.php';
$appEnv = $_ENV['APP_ENV'] ?? 'local';
$viewsPath = BASE_PATH.'/views';
$databaseUrl = 'pdo-mysql://root:root@127.0.0.1:3306/itpelag_blog?charset=utf8mb4';

$container = new Container();
$container->delegate(new ReflectionContainer(true));

$container->add('APP_ENV', new StringArgument($appEnv));

$container->add(ContainerInterface::class, $container);

$container->add(SessionInterface::class, Session::class);

$container->addShared(RouterInterface::class, Router::class);
$container->extend(RouterInterface::class)
    ->addMethodCall('registerRoutes', [new ArrayArgument($routes)]);

$container->inflector(AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

$container->add(ConnectionFactory::class)
    ->addArgument(new StringArgument($databaseUrl));

$container->addShared(Connection::class, function () use ($container): Connection {
    return $container->get(ConnectionFactory::class)->create();
});

$container->add('twig-factory', TwigFactory::class)
    ->addArguments([
        new StringArgument($viewsPath),
        SessionInterface::class
    ]);

$container->addShared('twig', function () use ($container): Environment {
    return $container->get('twig-factory')->create();
});

$container->add('console:migrate', MigrateCommand::class)
    ->addArguments([
        Connection::class,
        new StringArgument(BASE_PATH.'/database/migrations')
    ]);

return $container;
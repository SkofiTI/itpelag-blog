<?php

use App\Services\UserService;
use Doctrine\DBAL\Connection;
use Framework\Authentication\SessionAuthentication;
use Framework\Authentication\SessionAuthInterface;
use Framework\Console\Commands\MigrateCommand;
use Framework\Controller\AbstractController;
use Framework\Dbal\ConnectionFactory;
use Framework\Http\Middleware\ExtractRouteInfo;
use Framework\Http\Middleware\RequestHandler;
use Framework\Http\Middleware\RequestHandlerInterface;
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

$dotenv = new Dotenv();
$dotenv->load(BASE_PATH.'/.env');

$routes = include BASE_PATH.'/routes/web.php';
$appEnv = $_ENV['APP_ENV'] ?? 'local';
$viewsPath = BASE_PATH.'/views';
$databaseUrl = 'pdo-mysql://root:root@127.0.0.1:3306/itpelag_blog?charset=utf8mb4';

$container = new Container();
$container->delegate(new ReflectionContainer(true));

$container->addShared('APP_ENV', new StringArgument($appEnv));

$container->addShared(ContainerInterface::class, $container);

$container->addShared(SessionInterface::class, Session::class);

$container->addShared(SessionAuthInterface::class, SessionAuthentication::class)
    ->addArguments([
        UserService::class,
        SessionInterface::class,
    ]);

$container->addShared(RequestHandlerInterface::class, RequestHandler::class)
    ->addArgument($container);

$container->addShared(RouterInterface::class, Router::class)
    ->addArgument($container);

$container->add(ExtractRouteInfo::class)
    ->addArgument(new ArrayArgument($routes));

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
        SessionInterface::class,
        SessionAuthInterface::class,
    ]);

$container->addShared('twig', function () use ($container): Environment {
    return $container->get('twig-factory')->create();
});

$container->add('console:migrate', MigrateCommand::class)
    ->addArguments([
        Connection::class,
        new StringArgument(BASE_PATH.'/database/migrations'),
    ]);

return $container;

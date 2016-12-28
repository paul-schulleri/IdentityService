<?php

use IdentityService\Config\ConfigProvider;
use IdentityService\Di;
use IdentityService\Router\IdentityRouter;
use Olando\Config\ConfigLoader;
use Olando\Router\NotFoundRouter;
use Olando\Router\RouterChain;
use Olando\ValueObject\AppVersion;
use Olando\ValueObject\Environment;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$environmentName = empty($_SERVER['APP_ENV']) ? 'production' : $_SERVER['APP_ENV'];
$environment = Environment::fromString($environmentName);

$configLoader = new ConfigLoader(
    $environment,
    __DIR__ . '/../config/',
    __DIR__ . '/../'
);

$appVersion = AppVersion::fromFile(__DIR__ . '/../version.php');

$config = new ConfigProvider($configLoader);

$di = new Di($config, $appVersion);

$router = new RouterChain();
$router->register(new IdentityRouter($di));
$router->register(new NotFoundRouter($di));

$request = Request::createFromGlobals();
$controllerInterface = $router->route($request);

/** @var Symfony\Component\HttpFoundation\Response $response */
$response = $controllerInterface->execute($request);
$response->send();

exit;

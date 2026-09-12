<?php

declare(strict_types=1);

use App\Application;
use DI\ContainerBuilder;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(dirname(__DIR__) . '/config/container.php');
$container = $containerBuilder->build();

$application = $container->has(Application::class)
    ? $container->get(Application::class)
    : new Application($container);
$application->run();


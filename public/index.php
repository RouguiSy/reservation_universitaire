<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

session_start();
require_once dirname(__DIR__) . '/config/database.php';

$dispatcher = simpleDispatcher(function (RouteCollector $routeCollector): void {
    $routes = require dirname(__DIR__) . '/routes/web.php';
    $routes($routeCollector);
});

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$routeInfo = $dispatcher->dispatch($requestMethod, $requestUri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Page non trouvee';
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header('Allow: ' . implode(', ', $routeInfo[1]));
        echo '405 - Methode non autorisee';
        break;
    case Dispatcher::FOUND:
        [$handler, $variables] = [$routeInfo[1], $routeInfo[2]];
        [$controllerClass, $method] = explode('@', $handler, 2);
        $container = require dirname(__DIR__) . '/config/container.php';
        $container->get($controllerClass)->{$method}(...array_map('intval', $variables));
        break;
}
<?php

declare(strict_types=1);

namespace App;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;
use Throwable;

class Application
{
    private Dispatcher $dispatcher;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            $routes = require_once dirname(__DIR__) . '/routes/web.php';
            $routes($r);
        });
    }

    public function run(): void
    {
        try {
            session_start();

            $httpMethod = $_SERVER['REQUEST_METHOD'];
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            if ($uri !== '/' && $uri !== '/home' && strpos($uri, '.css') === false) {
                $uri = rtrim($uri, '/');
                if (empty($uri)) {
                    $uri = '/';
                }
            }

            $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    $this->notFound();
                    break;
                case Dispatcher::METHOD_NOT_ALLOWED:
                    $this->methodNotAllowed();
                    break;
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];

                    if (is_string($handler) && strpos($handler, '@') !== false) {
                        [$controllerClass, $method] = explode('@', $handler);
                        $controller = $this->container->get($controllerClass);
                        $controller->$method(...array_values($vars));
                    }
                    break;
            }
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    private function notFound(): void
    {
        http_response_code(404);
        require_once dirname(__DIR__) . '/templates/error/404.php';
    }

    private function methodNotAllowed(): void
    {
        http_response_code(405);
        require_once dirname(__DIR__) . '/templates/error/405.php';
    }

    private function handleError(Throwable $e): void
    {
        http_response_code(500);
        require_once dirname(__DIR__) . '/templates/error/500.php';
    }
}

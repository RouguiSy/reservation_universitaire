<?php

declare(strict_types=1);

namespace App;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;

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

        if ($this->requiresAuthentication($uri) && !isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        if ($this->requiresAdmin($uri) && (($_SESSION['user']['role'] ?? null) !== 'admin')) {
            http_response_code(403);
            echo '<h1>403 - Acces reserve aux administrateurs</h1>';
            return;
        }

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
                    $arguments = array_map(
                        static fn (string $value): int|string => ctype_digit($value) ? (int) $value : $value,
                        array_values($vars)
                    );
                    $controller->$method(...$arguments);
                }
                break;
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
        echo '<h1>405 - Methode non autorisee</h1>';
        echo '<p>La methode HTTP utilisee n\'est pas autorisee pour cette URL.</p>';
        echo '<a href="/">Retour a l\'accueil</a>';
    }

    private function requiresAuthentication(string $uri): bool
    {
        return $uri === '/dashboard'
            || str_starts_with($uri, '/reservations')
            || in_array($uri, ['/salles/create', '/salles/store'], true)
            || preg_match('#^/salles/(toggle|delete)/\d+$#', $uri) === 1;
    }

    private function requiresAdmin(string $uri): bool
    {
        return $uri === '/dashboard'
            || in_array($uri, ['/salles/create', '/salles/store'], true)
            || preg_match('#^/salles/(toggle|delete)/\d+$#', $uri) === 1;
    }
}

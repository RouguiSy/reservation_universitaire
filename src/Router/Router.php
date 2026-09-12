<?php

declare(strict_types=1);

namespace App\Router;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

class Router implements RouterInterface
{
    private Dispatcher $dispatcher;
    private ?ContainerInterface $container;

    public function __construct(
        ?ContainerInterface $container = null,
        ?callable $routesCallable = null
    ) {
        $this->container = $container;

        $routes = $routesCallable ?? require dirname(__DIR__, 2) . '/routes/web.php';

        $this->dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $collector) use ($routes): void {
            $routes($collector);
        });
    }

    public function dispatch(string $httpMethod, string $uri): void
    {
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $this->handleNotFound();
                return;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->handleMethodNotAllowed($routeInfo[1] ?? []);
                return;

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                $arguments = array_map(
                    static fn (string $value): int|string => ctype_digit($value) ? (int) $value : $value,
                    array_values($vars)
                );

                $this->execute($handler, $arguments);
                return;
        }
    }

    /**
     * Exécute le gestionnaire de route (Closure, Callable ou [Controller, Method]).
     */
    public function execute(mixed $handler, array $arguments = []): void
    {
        if (is_callable($handler)) {
            $handler(...$arguments);
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;
            $controller = $this->instantiateController($controllerClass);
            $controller->$method(...$arguments);
            return;
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$controllerClass, $method] = explode('@', $handler);
            $controller = $this->instantiateController($controllerClass);
            $controller->$method(...$arguments);
            return;
        }

        throw new RuntimeException("Gestionnaire de route invalide.");
    }

    /**
     * Instancie le contrôleur directement au niveau du Router
     * en injectant les dépendances de services nécessaires.
     */
    public function instantiateController(string $controllerClass): object
    {
        $reflector = new ReflectionClass($controllerClass);
        $constructor = $reflector->getConstructor();

        if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
            return new $controllerClass();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $dependencyClass = $type->getName();

                if ($this->container !== null && $this->container->has($dependencyClass)) {
                    $dependencies[] = $this->container->get($dependencyClass);
                    continue;
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            } else {
                throw new RuntimeException(
                    "Impossible de resoudre la dependance '{$parameter->getName()}' pour {$controllerClass}."
                );
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    protected function handleNotFound(): void
    {
        if (function_exists('wantsJson') && wantsJson()) {
            json_response([
                'status' => 'error',
                'message' => function_exists('message') ? message('error.not_found', [], 'Ressource non trouvee (404).') : 'Ressource non trouvee (404).',
            ], 404);
            return;
        }

        http_response_code(404);
        $template404 = dirname(__DIR__, 2) . '/templates/error/404.php';
        if (file_exists($template404)) {
            require_once $template404;
        } else {
            echo "<h1>404 - Page non trouvee</h1>";
        }
    }

    protected function handleMethodNotAllowed(array $allowedMethods = []): void
    {
        if (function_exists('wantsJson') && wantsJson()) {
            json_response([
                'status' => 'error',
                'message' => function_exists('message') ? message('error.method_not_allowed', [], 'Methode HTTP non autorisee (405).') : 'Methode HTTP non autorisee (405).',
                'allowed_methods' => $allowedMethods,
            ], 405);
            return;
        }

        http_response_code(405);
        $notAllowedTitle = function_exists('message') ? message('error.method_not_allowed', [], 'Methode non autorisee') : 'Methode non autorisee';
        echo "<h1>405 - {$notAllowedTitle}</h1>";
        echo '<p>La methode HTTP utilisee n\'est pas autorisee pour cette URL.</p>';
        echo '<a href="/">Retour a l\'accueil</a>';
    }
}

<?php

declare(strict_types=1);

namespace App;

use App\Router\Router;
use App\Router\RouterInterface;
use App\Security\CsrfService;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;

class Application
{
    private RouterInterface $router;
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container,
        ?Capsule $capsule = null,
        ?RouterInterface $router = null
    ) {
        $this->container = $container;
        if ($capsule === null && $this->container->has(Capsule::class)) {
            $this->container->get(Capsule::class);
        }
        $this->router = $router ?? new Router($container);
    }

    public function run(): void
    {
        require_once __DIR__ . '/helpers.php';

        $session = $this->container->has(\App\Session\SessionManager::class)
            ? $this->container->get(\App\Session\SessionManager::class)
            : new \App\Session\SessionManager();
        $session->start();

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $_POST = array_merge($_POST, $decoded);
                }
            }
        }

        if (in_array($httpMethod, ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            $isJsonRequest = wantsJson();
            $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $csrfService = $this->container->has(CsrfService::class)
                ? $this->container->get(CsrfService::class)
                : new CsrfService();

            if (!$isJsonRequest && !$csrfService->validate(is_string($submittedToken) ? $submittedToken : null)) {
                http_response_code(403);
                $csrfMsg = message('csrf.invalid', [], 'Jeton CSRF invalide ou expire');
                echo "<h1>403 - {$csrfMsg}</h1>";
                echo '<p>Votre requete a ete bloquee pour des raisons de securite.</p>';
                echo '<p><a href="javascript:history.back()">Retour au formulaire</a></p>';
                return;
            }
        }

        if ($uri !== '/' && $uri !== '/home' && strpos($uri, '.css') === false) {
            $uri = rtrim($uri, '/');
            if (empty($uri)) {
                $uri = '/';
            }
        }

        $errorHandler = $this->container->has(\App\Middleware\ErrorHandlerMiddleware::class)
            ? $this->container->get(\App\Middleware\ErrorHandlerMiddleware::class)
            : new \App\Middleware\ErrorHandlerMiddleware();

        $errorHandler->process(function () use ($httpMethod, $uri, $session): void {
            if ($this->requiresAuthentication($uri) && !$session->hasUser()) {
                if (wantsJson()) {
                    json_response([
                        'status' => 'error',
                        'message' => message('auth.unauthorized', [], 'Authentification requise pour acceder a cette ressource.'),
                    ], 401);
                }
                header('Location: /login');
                return;
            }

            if ($this->requiresAdmin($uri) && !$session->isAdmin()) {
                if (wantsJson()) {
                    json_response([
                        'status' => 'error',
                        'message' => message('auth.forbidden', [], 'Acces reserve aux administrateurs.'),
                    ], 403);
                }
                http_response_code(403);
                $adminTitle = message('auth.forbidden', [], 'Acces reserve aux administrateurs');
                echo "<h1>403 - {$adminTitle}</h1>";
                return;
            }

            // Délégation complète de la résolution de route et de l'instanciation du contrôleur au Router
            $this->router->dispatch($httpMethod, $uri);
        });
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


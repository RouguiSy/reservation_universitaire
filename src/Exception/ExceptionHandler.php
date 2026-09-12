<?php

declare(strict_types=1);

namespace App\Exception;

use App\Session\SessionManager;
use App\Session\SessionManagerInterface;
use Throwable;

class ExceptionHandler
{
    public function __construct(
        private ?SessionManagerInterface $session = null
    ) {
        $this->session = $session ?? (function_exists('session') ? session() : new SessionManager());
    }

    public function handle(Throwable $exception): void
    {
        $statusCode = $this->determineStatusCode($exception);
        $message = $exception->getMessage();
        $errors = $this->extractErrors($exception);

        // 1. Réponse JSON pour API / AJAX
        if (function_exists('wantsJson') && wantsJson()) {
            json_response([
                'status'  => 'error',
                'message' => $message,
                'errors'  => $errors,
                'code'    => $statusCode,
            ], $statusCode);
            return;
        }

        // 2. Erreur de validation sur formulaire Web : redirection avec messages flash et anciens champs
        if ($exception instanceof ValidationException) {
            $form = $this->guessFormFromUri();
            $this->session->setFormErrors($form, $exception->getErreurs());
            $this->session->setFormOld($form, $_POST);
            $this->session->flash('error', message('validation.fix_errors'));

            $referer = $_SERVER['HTTP_REFERER'] ?? "/{$form}s/create";
            header("Location: {$referer}");
            exit;
        }

        // 2b. Conflit de salle ou regle metier sur formulaire Web : flash et redirection
        if ($exception instanceof BusinessException || $exception instanceof SalleIndisponibleException) {
            $form = $this->guessFormFromUri();
            $this->session->setFormOld($form, $_POST);
            $this->session->flash('error', $message);

            $referer = $_SERVER['HTTP_REFERER'] ?? "/{$form}s/create";
            header("Location: {$referer}");
            exit;
        }

        // 2c. Non autorise sur Web : flash et redirection vers login
        if ($exception instanceof UnauthorizedException) {
            $this->session->setFormErrors('auth', ['global' => $message]);
            $this->session->setFormOld('auth', ['email' => $_POST['email'] ?? '']);
            header('Location: /login');
            exit;
        }

        // 2d. Introuvable sur action Web POST/PUT/DELETE : flash et redirection
        if ($exception instanceof NotFoundException) {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
                $form = $this->guessFormFromUri();
                $this->session->flash('error', $message);
                $referer = $_SERVER['HTTP_REFERER'] ?? "/{$form}s";
                header("Location: {$referer}");
                exit;
            }
        }

        // 3. Rendu de la vue d'erreur HTML dédiée
        http_response_code($statusCode);
        $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

        $template = $this->determineView($statusCode);
        $viewPath = dirname(__DIR__, 2) . '/templates/' . $template;

        if (file_exists($viewPath)) {
            $titre = $this->determineTitle($statusCode);
            extract([
                'statusCode' => $statusCode,
                'titre'      => $titre,
                'message'    => $message,
                'errors'     => $errors,
                'exception'  => $exception,
                'debug'      => $debug,
            ]);
            require_once $viewPath;
            exit;
        }

        echo "<h1>Erreur {$statusCode}</h1><p>" . htmlspecialchars($message) . "</p>";
        exit;
    }

    public function determineStatusCode(Throwable $exception): int
    {
        if ($exception instanceof AppException) {
            return $exception->getStatusCode();
        }
        if ($exception instanceof ValidationException) {
            return 422;
        }
        if ($exception instanceof ReservationIntrouvableException) {
            return 404;
        }
        if ($exception instanceof SalleIndisponibleException) {
            return 409;
        }
        if ($exception instanceof \InvalidArgumentException) {
            return 400;
        }

        $code = (int) $exception->getCode();
        return ($code >= 400 && $code < 600) ? $code : 500;
    }

    public function extractErrors(Throwable $exception): array
    {
        if ($exception instanceof ValidationException) {
            return $exception->getErreurs();
        }
        if ($exception instanceof AppException) {
            return $exception->getErrors();
        }
        return [];
    }

    private function determineView(int $statusCode): string
    {
        if ($statusCode === 404 && file_exists(dirname(__DIR__, 2) . '/templates/error/404.php')) {
            return 'error/404.php';
        }
        if ($statusCode === 405 && file_exists(dirname(__DIR__, 2) . '/templates/error/405.php')) {
            return 'error/405.php';
        }
        if ($statusCode === 500 && file_exists(dirname(__DIR__, 2) . '/templates/error/500.php')) {
            return 'error/500.php';
        }
        return 'error/error.php';
    }

    private function determineTitle(int $statusCode): string
    {
        return match ($statusCode) {
            400 => message('error.bad_request', [], 'Requete invalide'),
            401 => message('error.unauthorized', [], 'Non autorise'),
            403 => message('error.forbidden', [], 'Acces interdit'),
            404 => message('error.not_found', [], 'Page non trouvee'),
            405 => message('error.method_not_allowed', [], 'Methode non autorisee'),
            409 => message('error.conflict', [], 'Conflit de ressource'),
            422 => message('error.unprocessable_entity', [], 'Donnees non traitables'),
            default => message('error.server_error', [], 'Erreur interne du serveur'),
        };
    }

    private function guessFormFromUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
        if (str_contains($uri, 'reservation')) {
            return 'reservation';
        }
        if (str_contains($uri, 'salle')) {
            return 'salle';
        }
        if (str_contains($uri, 'auth') || str_contains($uri, 'login')) {
            return 'auth';
        }
        return 'form';
    }
}

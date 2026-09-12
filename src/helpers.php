<?php

declare(strict_types=1);

use App\Security\CsrfService;
use App\Support\Message;
use App\View\JsonRenderer;
use App\View\ViewRenderer;

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return (new CsrfService())->getToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('view_renderer')) {
    function view_renderer(): ViewRenderer
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new ViewRenderer();
        }
        return $instance;
    }
}

if (!function_exists('wantsJson')) {
    function wantsJson(): bool
    {
        return view_renderer()->resolveFormat() === ViewRenderer::FORMAT_JSON;
    }
}

if (!function_exists('json_response')) {
    function json_response(mixed $data, int $statusCode = 200): void
    {
        $payload = is_array($data) ? $data : ['data' => $data];
        \App\View\RendererFactory::make(\App\View\ViewRenderer::FORMAT_JSON)->respond('', $payload, $statusCode);
    }
}

if (!function_exists('respond')) {
    function respond(string $template, array $data = [], int $statusCode = 200): void
    {
        view_renderer()->respond($template, $data, $statusCode);
    }
}

if (!function_exists('message')) {
    function message(string $key, array $replace = [], ?string $default = null): string
    {
        return Message::get($key, $replace, $default);
    }
}

if (!function_exists('trans')) {
    function trans(string $key, array $replace = [], ?string $default = null): string
    {
        return message($key, $replace, $default);
    }
}

if (!function_exists('get_request_data')) {
    function get_request_data(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return array_merge($_POST, $decoded);
                }
            }
        }
        return $_POST;
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new \App\Session\SessionManager();
        }

        if ($key === null) {
            return $instance;
        }

        return $instance->get($key, $default);
    }
}

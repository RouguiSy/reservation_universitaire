<?php

declare(strict_types=1);

namespace App\View;

class JsonRenderer implements RendererInterface
{
    public function render(string $view, array $data = [], int $statusCode = 200): string
    {
        $isSuccess = ($statusCode >= 200 && $statusCode < 400);

        if (isset($data['status'])) {
            $payload = $data;
        } else {
            $payload = [
                'status' => $isSuccess ? 'success' : 'error',
                'data'   => $data,
            ];
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $json !== false ? $json : '{}';
    }

    public function respond(string $view, array $data = [], int $statusCode = 200, bool $exit = true): void
    {
        http_response_code($statusCode);

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo $this->render($view, $data, $statusCode);

        if ($exit) {
            exit;
        }
    }
}

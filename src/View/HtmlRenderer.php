<?php

declare(strict_types=1);

namespace App\View;

use RuntimeException;

class HtmlRenderer implements RendererInterface
{
    private string $templateDir;

    public function __construct(?string $templateDir = null)
    {
        $this->templateDir = rtrim($templateDir ?? dirname(__DIR__, 2) . '/templates', '/\\');
    }

    public function render(string $view, array $data = [], int $statusCode = 200): string
    {
        $normalizedView = ltrim($view, '/\\');
        $templatePath = $this->templateDir . '/' . $normalizedView;

        if (!file_exists($templatePath)) {
            throw new RuntimeException("Vue introuvable : {$normalizedView}");
        }

        extract($data);
        ob_start();
        require $templatePath;
        return (string) ob_get_clean();
    }

    public function respond(string $view, array $data = [], int $statusCode = 200, bool $exit = true): void
    {
        http_response_code($statusCode);

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
        }

        echo $this->render($view, $data, $statusCode);

        if ($exit) {
            exit;
        }
    }
}

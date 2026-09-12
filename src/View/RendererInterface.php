<?php

declare(strict_types=1);

namespace App\View;

interface RendererInterface
{
    public function render(string $view, array $data = [], int $statusCode = 200): string;
    public function respond(string $view, array $data = [], int $statusCode = 200, bool $exit = true): void;
}

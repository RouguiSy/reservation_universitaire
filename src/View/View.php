<?php

declare(strict_types=1);

namespace App\View;

class View
{
    public static function render(string $template, array $data = []): string
    {
        return RendererFactory::make(ViewRenderer::FORMAT_HTML)->render($template, $data);
    }
}

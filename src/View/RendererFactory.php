<?php

declare(strict_types=1);

namespace App\View;

use InvalidArgumentException;

class RendererFactory implements RendererFactoryInterface
{

    private array $resolvers = [];

    private ?string $templateDir;

    public function __construct(?string $templateDir = null)
    {
        $this->templateDir = $templateDir;
        $this->registerDefaults();
    }

    private function registerDefaults(): void
    {
        $this->register(ViewRenderer::FORMAT_HTML, fn() => new HtmlRenderer($this->templateDir));
        $this->register(ViewRenderer::FORMAT_JSON, fn() => new JsonRenderer());
    }


    public function register(string $format, callable|RendererInterface|string $resolver): void
    {
        $this->resolvers[strtolower(trim($format))] = $resolver;
    }

    public function create(string $format): RendererInterface
    {
        $normalized = strtolower(trim($format));

        if (!isset($this->resolvers[$normalized])) {
            $supported = implode(', ', array_keys($this->resolvers));
            throw new InvalidArgumentException("Format de rendu non supporte : '{$format}'. Formats disponibles : {$supported}");
        }

        $resolver = $this->resolvers[$normalized];

        if ($resolver instanceof RendererInterface) {
            return $resolver;
        }

        if (is_callable($resolver)) {
            $instance = $resolver();
            if ($instance instanceof RendererInterface) {
                return $instance;
            }
        }

        if (is_string($resolver) && class_exists($resolver)) {
            $instance = new $resolver();
            if ($instance instanceof RendererInterface) {
                return $instance;
            }
        }

        throw new InvalidArgumentException("Impossible d'instancier un RendererInterface pour le format '{$format}'.");
    }

    public static function make(string $format, ?string $templateDir = null): RendererInterface
    {
        return (new self($templateDir))->create($format);
    }
}

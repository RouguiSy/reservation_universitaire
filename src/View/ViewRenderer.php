<?php

declare(strict_types=1);

namespace App\View;

use App\View\Negotiation\FormatNegotiator;
use App\View\Negotiation\FormatNegotiatorInterface;

class ViewRenderer implements RendererInterface
{
    public const FORMAT_HTML = FormatNegotiator::FORMAT_HTML;
    public const FORMAT_JSON = FormatNegotiator::FORMAT_JSON;

    private RendererFactoryInterface $factory;
    private FormatNegotiatorInterface $negotiator;

    public function __construct(
        ?RendererFactoryInterface $factory = null,
        ?FormatNegotiatorInterface $negotiator = null
    ) {
        $this->factory = $factory ?? new RendererFactory();
        $this->negotiator = $negotiator ?? new FormatNegotiator();
    }

    public function getFactory(): RendererFactoryInterface
    {
        return $this->factory;
    }

    public function getNegotiator(): FormatNegotiatorInterface
    {
        return $this->negotiator;
    }

    public function resolveFormat(?string $formatOverride = null): string
    {
        return $this->negotiator->negotiate($formatOverride);
    }

    public function getRenderer(?string $format = null): RendererInterface
    {
        $resolvedFormat = $this->resolveFormat($format);
        return $this->factory->create($resolvedFormat);
    }

    public function render(string $view, array $data = [], int $statusCode = 200, ?string $format = null): string
    {
        return $this->getRenderer($format)->render($view, $data, $statusCode);
    }

    public function respond(string $view, array $data = [], int $statusCode = 200, bool $exit = true, ?string $format = null): void
    {
        $this->getRenderer($format)->respond($view, $data, $statusCode, $exit);
    }
}

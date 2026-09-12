<?php

declare(strict_types=1);

namespace App\View\Negotiation;

class FormatNegotiator implements FormatNegotiatorInterface
{
    public const FORMAT_HTML = 'html';
    public const FORMAT_JSON = 'json';
    public const DEFAULT_FORMAT = self::FORMAT_HTML;

    public const SUPPORTED_FORMATS = [
        self::FORMAT_HTML,
        self::FORMAT_JSON,
    ];

    private array $providers;

    private string $defaultFormat;
    public function __construct(?array $providers = null, string $defaultFormat = self::DEFAULT_FORMAT)
    {
        $this->defaultFormat = $defaultFormat;
        $this->providers = $providers ?? $this->defaultProviders();
    }

    public function negotiate(?string $override = null): string
    {
        foreach ($this->providers as $provider) {
            $candidate = strtolower(trim((string) $provider($override)));
            if (in_array($candidate, self::SUPPORTED_FORMATS, true)) {
                return $candidate;
            }
        }

        return $this->defaultFormat;
    }

    public function addProvider(callable $provider, bool $prepend = false): self
    {
        if ($prepend) {
            array_unshift($this->providers, $provider);
        } else {
            $this->providers[] = $provider;
        }

        return $this;
    }
    private function defaultProviders(): array
    {
        return [
            fn(?string $override): ?string => $override,
            fn(): ?string => $_GET['format'] ?? null,
            fn(): ?string => str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ? self::FORMAT_JSON : null,
            fn(): ?string => str_contains($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '', 'application/json') ? self::FORMAT_JSON : null,
            fn(): ?string => str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/') ? self::FORMAT_JSON : null,
            fn(): ?string => $_ENV['APP_RESPONSE_FORMAT'] ?? null,
        ];
    }
}

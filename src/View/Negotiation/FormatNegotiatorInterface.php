<?php

declare(strict_types=1);

namespace App\View\Negotiation;

interface FormatNegotiatorInterface
{

    public function negotiate(?string $override = null): string;

    public function addProvider(callable $provider, bool $prepend = false): self;
}

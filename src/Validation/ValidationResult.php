<?php

declare(strict_types=1);

namespace App\Validation;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Traversable;
use ArrayIterator;

final class ValidationResult implements ArrayAccess, IteratorAggregate, Countable
{
    public function __construct(
        private readonly bool $valid = true,
        private readonly array $errors = [],
        private readonly array $data = []
    ) {
    }

    public static function succes(array $data = []): self
    {
        return new self(valid: true, errors: [], data: $data);
    }

    public static function echec(array $errors, array $data = []): self
    {
        return new self(valid: false, errors: $errors, data: $data);
    }

    public function estValide(): bool
    {
        return $this->valid;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErreurs(): array
    {
        return $this->errors;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function getDonnees(): array
    {
        return $this->data;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function reponse(string $cle): ?string
    {
        return $this->errors[$cle] ?? null;
    }

    public function get(string $cle): ?string
    {
        return $this->errors[$cle] ?? null;
    }

    public function hasErrorsFor(string $champ): bool
    {
        return isset($this->errors[$champ]);
    }

    public function getErrorsFor(string $champ): ?string
    {
        return $this->errors[$champ] ?? null;
    }

    public function getPremiereErreur(): ?string
    {
        return $this->errors[array_key_first($this->errors)] ?? null;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->errors[(string) $offset]);
    }

    public function offsetGet(mixed $offset): ?string
    {
        return $this->errors[(string) $offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
    }

    public function offsetUnset(mixed $offset): void
    {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->errors);
    }

    public function count(): int
    {
        return count($this->errors);
    }
}

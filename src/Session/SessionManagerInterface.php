<?php

declare(strict_types=1);

namespace App\Session;

interface SessionManagerInterface
{
    public function start(array $options = []): void;

    public function isStarted(): bool;

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function has(string $key): bool;

    public function remove(string $key): void;

    public function clear(): void;

    public function all(): array;

    public function regenerate(bool $deleteOldSession = true): bool;

    public function destroy(): void;

    public function flash(string $type, string $message): void;

    public function getFlash(): ?array;

    public function hasFlash(): bool;

    public function setUser(array $user): void;

    public function getUser(): ?array;

    public function hasUser(): bool;

    public function removeUser(): void;

    public function isAdmin(): bool;

    public function setFormErrors(string $form, array $errors): void;

    public function getFormErrors(string $form): array;

    public function setFormOld(string $form, array $old): void;

    public function getFormOld(string $form): array;
}

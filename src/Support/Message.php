<?php

declare(strict_types=1);

namespace App\Support;

class Message
{
    private static ?array $messages = null;

    public static function load(?array $messages = null): void
    {
        if ($messages !== null) {
            self::$messages = $messages;
            return;
        }

        $filePath = dirname(__DIR__, 2) . '/config/messages.php';
        if (file_exists($filePath)) {
            $data = require $filePath;
            self::$messages = is_array($data) ? $data : [];
        } else {
            self::$messages = [];
        }
    }

    public static function get(string $key, array $replace = [], ?string $default = null): string
    {
        if (self::$messages === null) {
            self::load();
        }

        $segments = explode('.', $key);
        $current = self::$messages;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default ?? $key;
            }
            $current = $current[$segment];
        }

        if (!is_string($current)) {
            return $default ?? $key;
        }

        $text = $current;
        foreach ($replace as $placeholder => $value) {
            $rawPlaceholder = ltrim((string) $placeholder, ':');
            $text = str_replace(
                [':' . $rawPlaceholder, '{' . $rawPlaceholder . '}'],
                (string) $value,
                $text
            );
        }

        return $text;
    }

    public static function all(): array
    {
        if (self::$messages === null) {
            self::load();
        }

        return self::$messages ?? [];
    }

    public static function reset(): void
    {
        self::$messages = null;
    }
}

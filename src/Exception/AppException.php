<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;

abstract class AppException extends Exception
{
    public function __construct(
        string $message = '',
        private readonly int $statusCode = 500,
        private readonly array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

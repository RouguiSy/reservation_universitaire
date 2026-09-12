<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class UnauthorizedException extends AppException
{
    public function __construct(
        string $message = 'Authentification requise.',
        int $statusCode = 401,
        array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errors, $previous);
    }
}

<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class ForbiddenException extends AppException
{
    public function __construct(
        string $message = 'Acces interdit.',
        int $statusCode = 403,
        array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errors, $previous);
    }
}

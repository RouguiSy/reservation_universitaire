<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class AlreadyExistsException extends AppException
{
    public function __construct(
        string $message = 'La ressource existe deja.',
        int $statusCode = 409,
        array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errors, $previous);
    }
}

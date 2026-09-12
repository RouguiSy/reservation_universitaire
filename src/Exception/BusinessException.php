<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class BusinessException extends AppException
{
    public function __construct(
        string $message = 'Une regle metier a ete violee.',
        int $statusCode = 422,
        array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errors, $previous);
    }
}

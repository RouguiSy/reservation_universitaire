<?php

declare(strict_types=1);

namespace App\Exception;

use Throwable;

class NotFoundException extends AppException
{
    public function __construct(
        string $message = 'Ressource non trouvee.',
        int $statusCode = 404,
        array $errors = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $errors, $previous);
    }
}

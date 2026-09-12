<?php

declare(strict_types=1);

namespace App\Exception;

class ValidationException extends AppException
{
    public function __construct(private readonly array $erreurs, string $message = 'Les donnees envoyees sont invalides.')
    {
        parent::__construct($message, 422, $erreurs);
    }

    public function getErreurs(): array
    {
        return $this->erreurs;
    }

    public function errors(): array
    {
        return $this->erreurs;
    }
}

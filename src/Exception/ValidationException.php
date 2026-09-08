<?php

declare(strict_types=1);

namespace App\Exception;

class ValidationException extends \InvalidArgumentException
{
    public function __construct(private readonly array $erreurs)
    {
        parent::__construct('Les donnees envoyees sont invalides.');
    }

    public function getErreurs(): array
    {
        return $this->erreurs;
    }
}
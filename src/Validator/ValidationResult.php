<?php

declare(strict_types=1);

namespace App\Validator;

class ValidationResult
{
    private bool $valide;
    private array $erreurs;

    private function __construct(bool $valide, array $erreurs = [])
    {
        $this->valide = $valide;
        $this->erreurs = $erreurs;
    }

    public static function succes(): self
    {
        return new self(true);
    }

    public static function echec(array $erreurs): self
    {
        return new self(false, $erreurs);
    }

    public function estValide(): bool
    {
        return $this->valide;
    }

    public function getErreurs(): array
    {
        return $this->erreurs;
    }

    public function getPremiereErreur(): ?string
    {
        return $this->erreurs[array_key_first($this->erreurs)] ?? null;
    }

    public function ajouterErreur(string $champ, string $message): self
    {
        $this->erreurs[$champ] = $message;
        return $this;
    }
}

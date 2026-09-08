<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validator\SalleValidator;

class CreerSalleDTO
{
    public readonly string $nom;
    public readonly string $batiment;
    public readonly int $capacite;
    public readonly string $type;
    public readonly bool $active;

    public function __construct(
        string $nom,
        string $batiment,
        int $capacite,
        string $type,
        bool $active = true
    ) {
        $this->nom = $nom;
        $this->batiment = $batiment;
        $this->capacite = $capacite;
        $this->type = $type;
        $this->active = $active;
    }

    public static function depuisTableau(array $data): self
    {
        $validator = new SalleValidator();
        $result = $validator->validate($data);

        if (!$result->estValide()) {
            throw new \InvalidArgumentException(
                'Donnees invalides: ' . implode(', ', $result->getErreurs())
            );
        }

        return new self(
            $data['nom'],
            $data['batiment'],
            (int) $data['capacite'],
            $data['type'],
            isset($data['active']) ? (bool) $data['active'] : true
        );
    }

    public function toArray(): array
    {
        return [
            'nom' => $this->nom,
            'batiment' => $this->batiment,
            'capacite' => $this->capacite,
            'type' => $this->type,
            'active' => $this->active
        ];
    }
}

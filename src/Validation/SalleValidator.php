<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Validator as v;

final class SalleValidator extends AbstractValidator
{
    private const TYPES_AUTORISES = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];

    protected function messages(): array
    {
        return [
            'nom'      => 'Le nom est invalide.',
            'batiment' => 'Le batiment est invalide.',
            'capacite' => 'La capacite est invalide.',
            'type'     => 'Le type est invalide.',
            'active'   => "L'etat est invalide.",
        ];
    }

    protected function rules(array $data): array
    {
        return [
            'nom'      => v::stringType()->notEmpty()->length(2, 100),
            'batiment' => v::stringType()->notEmpty()->length(2, 100),
            'capacite' => v::intVal()->between(1, 1000),
            'type'     => v::in(self::TYPES_AUTORISES),
            'active'   => v::optional(v::boolVal()),
        ];
    }
}
